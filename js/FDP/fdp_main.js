/**
 * Manage all custom editor component
 *
 * @package editor
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
 
 function pouet(myEditable)
 {
 alert("oo");
 $(myEditable).parent().parent().parent().parent().parent().parent().parent().parent().remove();
 
 }
 
var FDPControllerImpl = function() {

    /* Enable Drag and Drop properties */
    function activateDragAndDrop() {

		alert("drag & drop");
		//All the element with JDRollUserControl (item to drop) CSS class are set to draggable
		$(".JDRollUserControl").draggable({
			helper: 'clone',
			cursor: 'move',
			cancel: null,
			containment: "parent"
		 });

        //All the element with JDRollDroppedUserControl (already dropped items) CSS class are set to draggable
		 $(".JDRollDroppedUserControl").draggable({
			cursor: 'move',
			containment: "parent"
		});
		 
		// Only the element with JDRollDropZone CSS class are set to droppable
		$('.JDRollDropZone').droppable({
			activeClass: 'ui-state-hover',
			accept: '.JDRollUserControl',
			drop: function(event, ui)
			{
                //Do not clone already dropped items !
				if (!ui.draggable.hasClass("JDRollDroppedUserControl"))
				{

					var cle = jQuery(ui.draggable).clone();

                    //Calculating position - Tested on Chrome 36, IE 11
                    xx = parseInt(ui.offset.left - $(this).offset().left) -2;
                    yy = parseInt(ui.offset.top - $(this).offset().top) - 1;

					cle.css({
					  position:"absolute",
					  top: yy ,
					  left: xx
					});
					cle.attr('id', 'JDRollUserControl_' + nextIndex()).removeClass("JDRollUserControl").addClass("JDRollDroppedUserControl").draggable({
					  cursor: 'move',
					  containment: "parent"
					}).resizable();
					if(cle.children('a').attr('data-type') === "select2")
					{
						cle.children('a').editable({source: [
							{id: 'gb', text: 'Great Britain'},
							{id: 'us', text: 'United States'},
							{id: 'ru', text: 'Russia'}
							]}).attr('id','JDRollUserControlLink' + count + '_child');
					}
					else
						cle.children('a').editable(/*{adminmode: 1}*/).attr('id','JDRollUserControlLink' + count + '_child');
					$(this).append(cle);
				}
			}
		});
    }

    /* Activate editable functionality on existing element */
	function activateEditableFields() {

		$('a[id^="JDRollUserControlLink"]').each(function(){
			$(this).editable();
		});
    }

    /* Activate resizable functionality on existing element */
	function activateRezisableFunc() {

		$(".JDRollDroppedUserControl").resizable().resizable('destroy');
        $(".JDRollDroppedUserControl").resizable();
    }

    return {
        InitFDP : function(mode) {
		
			if(mode === 1)
			{
				activateDragAndDrop();
				activateRezisableFunc();
				activateEditableFields();
			}
        }
	};
};
var FDPController = FDPControllerImpl();
