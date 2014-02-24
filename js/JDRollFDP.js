function initFDP(element,showEmpty) {

element.find('a[id^="JDRollUserControlLink"]').each(function(){

		var isEmpty = 0;
		if($(this).hasClass("editable-empty"))
			isEmpty = 1;
		
		
		if(element.has("#" + $(this).attr('id')  +  "_hidden").length > 0)
			{
				$(this).text($('#' + $(this).attr('id')  +  '_hidden').val());
				$(this).removeClass("editable-empty");
				isEmpty = 0;
			}
		
		$(this).editable({
			value:  $('#' + $(this).attr('id')  +  '_hidden').val(),
			success: function(response, newValue) {
				if(newValue.city)
				{
					if($(this).css('color') == 'rgb(221, 17, 68)')
								$(this).css('color','#0088cc');
					$(this).text(newValue.city);
					if($(this).parent().parent().has('#' + $(this).attr('id')  +  '_hidden').length > 0)
						$(this).parent().parent().find('#' + $(this).attr('id')  +  '_hidden').val(newValue.city);
					else
						$(this).parent().append('<input type="hidden" id="' + $(this).attr('id')  +  '_hidden" value="' + newValue.city + '"/>');
				}
				else
				{
						
					if(newValue.city == "")
					{
						if($(this).css('color') == 'rgb(0, 136, 204)')
								$(this).css('color','#DD1144');
						if($(this).parent().parent().has('#' + $(this).attr('id')  +  '_hidden').length > 0)
						{

							$(this).parent().parent().find('#' + $(this).attr('id')  +  '_hidden').val('');
						}
						else
						{
							$(this).parent().append("<input type='hidden' id='" + $(this).attr('id')  +  "_hidden' value='' />");
						}
					}
					else
					{
						if(newValue == "")
						{
							if($(this).css('color') == 'rgb(0, 136, 204)')
								$(this).css('color','#DD1144');
						}
						else
						{
							if($(this).css('color') == 'rgb(221, 17, 68)')
								$(this).css('color','#0088cc');
								
						}
							
						if($(this).parent().parent().has('#' + $(this).attr('id')  +  '_hidden').length > 0)
						{
							
							$(this).parent().parent().find('#' + $(this).attr('id')  +  '_hidden').val(newValue);
						}
						else
						{
							$(this).parent().append('<input type="hidden" id="' + $(this).attr('id')  +  '_hidden" value="' + newValue + '"/>');
						}
					}
				}
			}
		});
		
		if(isEmpty)
		{
			$(this).addClass("editable-empty");
			if(!showEmpty)
			{
				$(this).css("display","none");
			}
			
		}
	});
}