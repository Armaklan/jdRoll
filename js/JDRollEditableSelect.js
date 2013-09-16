/**
Address editable input.
Internally value stored as {city: "Moscow", street: "Lenina", building: "15"}

@class address
@extends abstractinput
@final
@example
<a href="#" id="address" data-type="address" data-pk="1">awesome</a>
<script>
$(function(){
    $('#address').editable({
        url: '/post',
        title: 'Enter city, street and building #',
        value: {
            city: "Moscow", 
            street: "Lenina", 
            building: "15"
        }
    });
});
</script>
**/
(function ($) {
    "use strict";
    
    var JDRollEditableSelect = function (options) {
		if(options.adminmode ==  1)
			JDRollEditableSelect.defaults.tpl = '<div class="editable-JDRollEditableSelect"><select></select></div><div class="editable-JDRollEditableSelect"><input type="text" name="city" class="input-small" class="editable-JDRollEditableSelect"><div class=\"editable-buttons editable-JDRollEditableSelect\"><button type=\"button\" class=\"btn \" onclick=\"var h = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().attr(\'id\') + \'_hide\'; var v = \'\'; if(document.getElementById(h)) v = document.getElementById(h).value;  v += $(this).parent().parent().children(\'input\').val(); $(this).parent().parent().siblings().children(\'select\').append(\'<option>\' + $(this).parent().parent().children(\'input\').val() + \'</option>\'); if(document.getElementById(h)) document.getElementById(h).value = v + \',\'; else $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().append(\'<input type=\\\'hidden\\\' id=\\\'\' + $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().attr(\'id\') + \'_hide\\\' value=\\\'\' + v + \',\\\'  />\');"  ><i class=\" \">+</i></button><button type=\"button\" class=\"btn \" onclick=\"var h = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().attr(\'id\') + \'_hide\'; var v = \'\'; var vToDel = $(this).parent().parent().children(\'input\').val(); $(this).parent().parent().siblings().children(\'select\').children(\'option[value=\' + vToDel + \']\').remove(); $(this).parent().parent().siblings().children(\'select\').children().each(function(){ v+= $(this).text() + \',\';}); if(document.getElementById(h)) document.getElementById(h).value = v; "><i class=\"\">-</i></button></div>';
		else
			JDRollEditableSelect.defaults.tpl = '<div class="editable-JDRollEditableSelect"><select></select></div>';
        this.init('JDRollEditableSelect', options, JDRollEditableSelect.defaults);
    };

    //inherit from Abstract input
    $.fn.editableutils.inherit(JDRollEditableSelect, $.fn.editabletypes.abstractinput);

    $.extend(JDRollEditableSelect.prototype, {
        /**
        Renders input from tpl

        @method render() 
        **/        
        render: function() {
           this.$input = this.$tpl.find('input');
		   this.$select = this.$tpl.find('select');
        },
        
        /**
        Default method to show value in element. Can be overwritten by display option.
        
        @method value2html(value, element) 
        **/
        value2html: function(value, element) {
		//alert(value.city);
            if(!value || value.city == '') {
                $(element).empty();
                return; 
            }
            //var html = $(this).text(value.city);
            $(element).text(value.city); 
        },
        
        /**
        Gets value from element's html
        
        @method html2value(html) 
        **/        
        html2value: function(html) {
			//alert('html2value');
          /*
            you may write parsing method to get value by element's html
            e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
            but for complex structures it's not recommended.
            Better set value directly via javascript, e.g. 
            editable({
                value: {
                    city: "Moscow", 
                    street: "Lenina", 
                    building: "15"
                }
            });
          */ 
		  
		  
		  return null;  
        },
      
       /**
        Converts value to string. 
        It is used in internal comparing (not for sending to server).
        
        @method value2str(value)  
       **/
       value2str: function(value) {
	     //alert('value2str');
           var str = '';
           if(value) {
               for(var k in value) {
                   str = str + k + ':' + value[k] + ';';  
               }
           }
           return str;
       }, 
       
       /*
        Converts string to value. Used for reading value from 'data-value' attribute.
        
        @method str2value(str)  
       */
       str2value: function(str) {
           /*
           this is mainly for parsing value defined in data-value attribute. 
           If you will always set value by javascript, no need to overwrite it
           */
           return str;
       },                
       
       /**
        Sets value of input.
        
        @method value2input(value) 
        @param {mixed} value
       **/         
       value2input: function(value) {
	   //alert('value2input');
	   
           

		   
		   
		  var h = this.$select.parent().parent().parent().parent().parent().parent().parent().parent().parent().attr('id') + '_hide';
		  //alert(h);
		  var v = ''; 
		  if(document.getElementById(h)) 
		  {
				//alert(document.getElementById(h).value);
				var tab = document.getElementById(h).value.split(',');
				 var i = 0;
				// alert(tab.length);
				this.$select.append('<option value=""></option>');
				for(i =0;i<tab.length;i++)
				{
				//	alert(tab[i]);
					if(tab[i] != '')
					this.$select.append('<option value="' + tab[i] + '">' + tab[i] + '</option>');
				}
			}
			if(!value) {
             return;
           }
		   
		   
		   //alert(value.city);
		   //alert(value);

			this.$select.children('[value="' + value.city + '"]').attr('selected','selected');
           // this.$input.filter('[name="street"]').val(value.street);
           // this.$input.filter('[name="building"]').val(value.building);
       },       
       
       /**
        Returns value of input.
        
        @method input2value() 
       **/          
       input2value: function() { 
	   //alert('input2value');
           return {
				city: this.$select.val()
               //city: this.$input.filter('[name="city"]').val(), 
              // street: this.$input.filter('[name="street"]').val(), 
              // building: this.$input.filter('[name="building"]').val()
           };
       },        
       
        /**
        Activates input: sets focus on the first field.
        
        @method activate() 
       **/        
       activate: function() {
            this.$input.focus();
       },  
       
       /**
        Attaches handler to submit form in case of 'showbuttons=false' mode
        
        @method autosubmit() 
       **/       
       autosubmit: function() {
           this.$input.keydown(function (e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
           });
       }       
    });

    JDRollEditableSelect.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        tpl: '',
        inputclass: '',
		adminmode: '',
		emptytext: ''
		
    });

    $.fn.editabletypes.JDRollEditableSelect = JDRollEditableSelect;

}(window.jQuery));