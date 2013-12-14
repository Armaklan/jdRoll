
  var selected = [];


  if (navigator.userAgent.indexOf("IE") != -1) {
    tinymce.init({
      selector: ".wysiwyg",
      plugins: [
      "link image lists preview hr insertdatetime",
      "table template textcolor fullscreen",
      "emoticons code spellchecker"
      ],
      browser_spellcheck : true,
      convert_urls: false,
      spellchecker_languages : "+French=fr",
      spellchecker_rpc_url : "{{app.request.baseUrl}}/tinymce/plugins/spellchecker/rpc.php",
      toolbar: "cut copy paste | styleselect removeformat | bold italic forecolor | alignleft aligncenter alignright alignjustify | hr | bullist numlist outdent indent | link image | preview fullscreen | emoticons private hide",
      style_formats: [
      {title: 'Dialogue', inline: 'span', classes: 'dialogue', styles : {color : '#4488CC'}},
      {title: 'Pensée', inline: 'span', classes: 'pensee', styles : {color : '#8844CC'}},
      {title: 'Hrp', inline: 'span', classes: 'hrp', styles : {color : 'grey'}},
      {title: 'Titre 1', block : 'h1'},
      {title: 'Titre 2', block : 'h2'},
      {title: 'Titre 3', block : 'h3'}
      ],
      autosave_ask_before_unload: false,
      setup: function(editor) {
       editor.addButton('private', {
        text: 'Prv',
        icon: false,
        onclick: function() {
          editor.windowManager.open({
                title: 'Message privée',
                body: [
                    {type: 'listbox', 
                        name: 'users', 
                        multiple: 'true',
                        size: 'large',
                        width: '200',
                        values: specificValue,
                        onselect: function(e) {
                            if (selected.indexOf(e.control.value()) == -1) {
                              selected.push(e.control.value());
                            }  else {
                              selected.splice(e.control.value());
                            } 
                        }
                    }
                ],
                onsubmit: function(e) {
                    var content = tinyMCE.activeEditor.selection.getContent();
                    editor.insertContent('[private='+selected.join()+']' + content + '[/private]');
                }
            });
        }
      });
       editor.addButton('hide', {
        text: 'Hid',
        icon: false,
        onclick: function() {
          var content = tinyMCE.activeEditor.selection.getContent();
          editor.insertContent('[hide]' + content + '[/hide]');
        }
      });
     },
     min_height: 160
   });
  } else {
    if( window.matchMedia("(min-width: 600px)").matches) {
      tinymce.init({
        selector: ".wysiwyg",
        plugins: [
        "link image lists preview hr insertdatetime",
        "table template textcolor fullscreen",
        "emoticons code spellchecker"
        ],
        browser_spellcheck : true,
        convert_urls: false,
        spellchecker_languages : "+French=fr",
        spellchecker_rpc_url : "{{app.request.baseUrl}}/tinymce/plugins/spellchecker/rpc.php",
        toolbar: "cut copy paste | styleselect removeformat | bold italic forecolor | alignleft aligncenter alignright alignjustify | hr | bullist numlist outdent indent | link image | preview fullscreen | emoticons private hide",
        style_formats: [
        {title: 'Dialogue', inline: 'span', classes: 'dialogue', styles : {color : '#4488CC'}},
        {title: 'Pensée', inline: 'span', classes: 'pensee', styles : {color : '#8844CC'}},
        {title: 'Hrp', inline: 'span', classes: 'hrp', styles : {color : 'grey'}},
        {title: 'Titre 1', block : 'h1'},
        {title: 'Titre 2', block : 'h2'},
        {title: 'Titre 3', block : 'h3'}
        ],
        autosave_ask_before_unload: false,
        setup: function(editor) {
         editor.addButton('private', {
          text: 'Prv',
          icon: false,
          onclick: function() {
            editor.windowManager.open({
                  title: 'Message privée',
                  body: [
                      {type: 'listbox', 
                          name: 'users', 
                          multiple: 'true',
                          size: 'large',
                          width: '200',
                          values: specificValue,
                          onselect: function(e) {
                              if (selected.indexOf(e.control.value()) == -1) {
                                selected.push(e.control.value());
                              }  else {
                                selected.splice(e.control.value());
                              } 
                          }
                      }
                  ],
                  onsubmit: function(e) {
                      var content = tinyMCE.activeEditor.selection.getContent();
                      editor.insertContent('[private='+selected.join()+']' + content + '[/private]');
                  }
              });
          }
        });
         editor.addButton('hide', {
          text: 'Hid',
          icon: false,
          onclick: function() {
            var content = tinyMCE.activeEditor.selection.getContent();
            editor.insertContent('[hide]' + content + '[/hide]');
          }
        });
       },
       min_height: 160
     });
  } else {
    tinymce.init({
      selector: ".wysiwyg",
      plugins: [
      "link image lists preview hr insertdatetime",
      "table template textcolor fullscreen",
      "emoticons code"
      ],
      menubar : false,
      browser_spellcheck : true,
      convert_urls: false,
      toolbar: "paste | styleselect removeformat | bold italic | hr | link image | code",
      style_formats: [
      {title: 'Dialogue', inline: 'span', classes: 'dialogue', styles : {color : '#4488CC'}},
      {title: 'Pensée', inline: 'span', classes: 'pensee', styles : {color : '#8844CC'}},
      {title: 'Hrp', inline: 'span', classes: 'hrp', styles : {color : 'grey'}},
      {title: 'Titre 1', block : 'h1'},
      {title: 'Titre 2', block : 'h2'},
      {title: 'Titre 3', block : 'h3'}
      ],
      autosave_ask_before_unload: false,
      min_height: 160,
      width: 300
    });
  }
}