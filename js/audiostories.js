/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */
$(document).ready(function() {
	$(".story-big, .story-small").mouseenter(function(){
		var i=$(".story-big, .story-small").index($(this));
		for(var j=0;j<$(".story-big, .story-small").length;j++){
			$('.color-'+j).removeClass('opaque');
		}
		$('.color-'+i).addClass('opaque');
	});
	if($('.newsok').length>0) {
		window.setTimeout(function(){
			$('.newsok').fadeOut(function(){window.location='/'});
		},5000);
	}
});
