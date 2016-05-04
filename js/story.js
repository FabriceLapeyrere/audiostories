/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
'use strict';
$(document).ready(function() {
	var itemsOk=[];
	var player=false;
	var Aplayer, Bplayer;
	var Aplaying=false;
	var Bplaying=false;
	var ATime=0;
	var BTime=0;
    var maj=function(sel){
		var myAudio=$(sel+' audio')[0];
		var s='';
		var percent=0;
		for (var i=0;i<myAudio.buffered.length;i++) {
			s+='<div class="p" style="position:absolute;height:100%;left:'+(100*myAudio.buffered.start(i)/myAudio.duration)+'%;width:'+(100*(myAudio.buffered.end(i)-myAudio.buffered.start(i))/myAudio.duration)+'%;"></div> ';
			percent+=100*(myAudio.buffered.end(i)-myAudio.buffered.start(i))/myAudio.duration;
			//console.log(100*(myAudio.buffered.end(i)-myAudio.buffered.start(i))/myAudio.duration, percent);
		}
		$(sel.replace('waveform','')+'progress').html(s);
		if (percent>99) {
			$(sel).next().animate({width:'100%'},800,function(){$(this).hide();});
		} else {
			setTimeout(function(){maj(sel);},100);
		}
	}
	var prep = function(json) {
		var arrayOfPeaksTmp=[];
		var arrayOfPeaks=[];
		var maximum=0;
		for (var i=0;i<json.data.length/2;i++) {
			maximum=Math.max(json.data[2*i+1],maximum);
		}
		for (var i=0;i<json.data.length/2;i++) {
			arrayOfPeaksTmp.push((json.data[2*i+1]-json.data[2*i])/(2*maximum*1.8));
		}
		var s= Smooth(arrayOfPeaksTmp);
		for (var i=0;i<json.data.length*5;i++) {
			arrayOfPeaks.push(s(i/10));
		}
		return arrayOfPeaks;
	}
	var APeaksOk=[];
	var BPeaksOk=[];
	if (APeaks!==0) APeaksOk=prep(APeaks);
	if (BPeaks!==0) BPeaksOk=prep(BPeaks);
	var waveInit=function(sel,sound,peaks){
		var wavesurfer = Object.create(WaveSurfer);
		wavesurfer.init({
		    container: document.querySelector(sel),
		    backend: 'AudioElement',
		    height:$('.carreparent').height()+2,
		    cursorColor:'rgba(0,0,0,0.1)',
		    progressColor:'#333',
		    waveColor:'#EEE',
		    hideScrollbar:true
		});
		wavesurfer.isPlaying=false;
		wavesurfer.on('play',function(){
			$('.playPause').removeClass('play').addClass('pause');
			if (sel=='.Awaveform') {$('.faceA-play').addClass('ok');$('.faceB').removeClass('ok');$('.faceA').addClass('ok');}
			if (sel=='.Bwaveform') {$('.faceB-play').addClass('ok');$('.faceA').removeClass('ok');$('.faceB').addClass('ok');}
			wavesurfer.isPlaying=true;
		});
		wavesurfer.on('audioprocess',function(t){
			var ts=Math.floor(t);
			var mins=Math.floor(ts/60);
			var s=ts%60;
			if (s<10) s='0'+s;
			var temps=mins+"'"+s+'"';
			if (sel=='.Awaveform') {
				var total=Math.floor(Aplayer.getDuration());
				var totalmins=Math.floor(total/60);
				var totals=total%60;
				if (totals<10) totals='0'+totals;
				var tempsTotal=totalmins+"'"+totals+'"';
			
				$('.Atime').html(temps+" / "+tempsTotal);
			}
			if (sel=='.Bwaveform') {
				var total=Math.floor(Bplayer.getDuration());
				var totalmins=Math.floor(total/60);
				var totals=total%60;
				if (totals<10) totals='0'+totals;
				var tempsTotal=totalmins+"'"+totals+'"';
			
				$('.Btime').html(temps+" / "+tempsTotal);
			}
		});
		wavesurfer.on('pause',function(){
			$('.playPause').removeClass('pause').addClass('play');
			if (sel=='.Awaveform') {$('.faceA-play').removeClass('ok');};
			if (sel=='.Bwaveform') {$('.faceB-play').removeClass('ok');};
			wavesurfer.isPlaying=false;
		});
		wavesurfer.on('ready',function(){
			var ts=0;
			var mins=Math.floor(ts/60);
			var s=ts%60;
			if (s<10) s='0'+s;
			var temps=mins+"'"+s+'"';
			if (sel=='.Awaveform') {
				var total=Math.floor(Aplayer.getDuration());
				var totalmins=Math.floor(total/60);
				var totals=total%60;
				if (totals<10) totals='0'+totals;
				var tempsTotal=totalmins+"'"+totals+'"';
			
				$('.Atime').html(temps+" / "+tempsTotal);
			}
			if (sel=='.Bwaveform') {
				var total=Math.floor(Bplayer.getDuration());
				var totalmins=Math.floor(total/60);
				var totals=total%60;
				if (totals<10) totals='0'+totals;
				var tempsTotal=totalmins+"'"+totals+'"';
			
				$('.Btime').html(temps+" / "+tempsTotal);
			}
			maj(sel);
		});
		wavesurfer.load(sound, peaks);
		return wavesurfer;
	}
	$('.meta').on('click','.faceA, .Ago.ok',function(){
		Bplayer.pause();
		$(".Bwaveform, .Bprogress, .Btime").addClass('farwest');
		$(".Awaveform, .Aprogress, .Atime").removeClass('farwest');
		$(".Ago").removeClass('ok');
		$(".Bgo").addClass('ok');
		Aplayer.play();
	});
	$('.meta').on('click','.faceB, .Bgo.ok',function(){
		Aplayer.pause();
		$(".Awaveform, .Aprogress, .Atime").addClass('farwest');
		$(".Bwaveform, .Bprogress, .Btime").removeClass('farwest');
		$(".Bgo").removeClass('ok');
		$(".Ago").addClass('ok');
		Bplayer.play();
	});
	$('.playPause').click(function(){
		if ($(".Bwaveform").hasClass('farwest')) Aplayer.playPause();
		if ($(".Awaveform").hasClass('farwest')) Bplayer.playPause();
	});
	var goTo=function(index){
		if (index==0) $('.prev').hide();
		else $('.prev').show();
		if (index==PairesOk.length-1) $('.next').hide();
		else $('.next').show();
		$('.diaporama .pgauche img').attr('src',PairesOk[index].gauche);
		$('.diaporama .pdroite img').attr('src',PairesOk[index].droite);
		$('.nav-item').each(function(i,e){
			if (i==index) $(e).addClass('ok');
			else $(e).removeClass('ok');
		});
	}
	$('.nav-item').click(function(){
		var index=$(this).index('.nav-item');
		goTo(index);
	});
	$('.prev').click(function(){
		var index=$('.nav-item.ok').index('.nav-item');
		if (index>0) goTo(index-1);
	});
	$('.next').click(function(){
		var index=$('.nav-item.ok').index('.nav-item');
		if (PairesOk[index+1] && PairesOk[index+1].gauche!='' && PairesOk[index+1].droite!='') goTo(index+1);
	});
	var imgs=[];
	var maj_nav=function(){
		for(var i=0;i<PairesOk.length;i++){
			if (PairesOk[i].gauche != '' && PairesOk[i].droite != '' ) {
				$('.nav-item:eq('+i+')').removeClass('load');
			}
		}
	};
	var loadImg=function(i){
		$('.nav-item:eq('+i+')').removeClass('lazy').addClass('load');
		if (i<Paires.length){
			var Igauche=new Image;
			var Idroite=new Image;
			Igauche.tmpsrc=Paires[i].gauche;
			Igauche.index=i;
			Idroite.tmpsrc=Paires[i].droite;
			Idroite.index=i;
			Igauche.onload=function(){
				PairesOk[this.index].gauche=this.src;
				if (PairesOk[this.index].droite!='') {
					maj_nav();
					loadImg(this.index+1)
					if (this.index==0) {
						goTo(0);
						if (faceA!==0) Aplayer=waveInit('.Awaveform',faceA, APeaksOk);
						if (faceB!==0) Bplayer=waveInit('.Bwaveform',faceB, BPeaksOk);
					}
				}
			};
			Idroite.onload=function(){
				PairesOk[this.index].droite=this.src;
				if (PairesOk[this.index].gauche!='') {
					maj_nav();
					loadImg(this.index+1);
					if (this.index==0) {
						goTo(0);
						if (faceA!==0) Aplayer=waveInit('.Awaveform',faceA, APeaksOk);
						if (faceB!==0) Bplayer=waveInit('.Bwaveform',faceB, BPeaksOk);
					}
				}
			};
			Igauche.src=Igauche.tmpsrc;
			Idroite.src=Idroite.tmpsrc;
			imgs.push({gauche:Igauche,droite:Idroite});
		}
	}
	loadImg(0);
	$(window).resize(function() {
		//Resize waveform
		$('.waveContainer, .Awaveform wave, .Awaveform canvas, .Bwaveform wave, .Bwaveform canvas').height($('.carreparent').height());
		var tA=Aplayer.getCurrentTime();
		var tB=Bplayer.getCurrentTime();
		var Ap=Aplayer.isPlaying;
		var Bp=Bplayer.isPlaying;
		Aplayer.empty();
		Aplayer.drawBuffer();
		Bplayer.empty();
		Bplayer.drawBuffer();
		Aplayer.seekTo(tA/Aplayer.getDuration());
		Bplayer.seekTo(tB/Bplayer.getDuration());
		if (Ap) Aplayer.play();
		if (Bp) Bplayer.play();
	});
	$('.waveContainer, .Awaveform wave, .Awaveform canvas, .Bwaveform wave, .Bwaveform canvas').height($('.carreparent').height()+2);
	$(document).keydown(function(evt) {
		if (evt.keyCode == 32) {
			$('.playPause').click();
			evt.preventDefault();
		}
		if (evt.keyCode == 37) {
			$('.prev').click();
			evt.preventDefault();
		}
		if (evt.keyCode == 39) {
			$('.next').click();
			evt.preventDefault();
		}
	});
});
