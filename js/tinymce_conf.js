var selected = [];

var formats = {dialogue: {
  title: 'Dialogue',
  inline: 'span',
  classes: 'dialogue'
}, pensee: {
  title: 'Pensée',
  inline: 'span',
  classes: 'pensee'
}, rp1: {
  title: 'RP1',
  inline: 'span',
  classes: 'rp1'
}, rp2: {
  title: 'RP2',
  inline: 'span',
  classes: 'rp2'
}, rp3: {
  title: 'Hrp',
  inline: 'span',
  classes: 'hrp'
}, t1: {
  title: 'Titre 1',
  block: 'h1'
}, t2: {
  title: 'Titre 2',
  block: 'h2'
}, t3: {
  title: 'Titre 3',
  block: 'h3'
}, blockquote: {
  title: 'Citation',
  block: 'blockquote'
}};

var fontFormat = "Standard=Helvetica Neue, Helvetica, Arial, sans-serif;" +
    "Police Lcd=LiquidCrystal;"+
    "Police Runique=Runes;"+
    "Police Elfique=Tengwar";

var configBase = {
  plugins: [
    "link image lists hr",
    "table textcolor fullscreen",
    "emoticons code"
  ],
  content_css : BASE_PATH + "/css/main.css",
  browser_spellcheck: true,
  convert_urls: false,
  toolbar: "cut copy paste | styleselect fontselect removeformat | bold italic forecolor | alignleft aligncenter alignright alignjustify | hr | bullist numlist outdent indent | link image | fullscreen | emoticons private hide popup perso perso2 carte",
  style_formats: formats,
  formats: formats,
  autosave_ask_before_unload: false,
  font_formats: fontFormat,
  setup: setupCustomIco
};

var configPost = jQuery.extend(true, {}, configBase);
configPost.min_height = 160;
configPost.selector = ".wysiwyg";

var configMobile = jQuery.extend(true, {}, configBase);
configMobile.plugins = [
  "link image lists hr",
  "textcolor fullscreen"
];
configMobile.toolbar = "styleselect removeformat | bold italic | link image | hr | private hide popup perso perso2 carte";
configMobile.menubar = false;
configMobile.min_height = 400;
configMobile.selector = ".wysiwyg";

if (navigator.userAgent.indexOf("IE") != -1) {
  tinymce.init(configPost);
} else {
  if (window.matchMedia("(min-width: 600px)").matches) {
    tinymce.init(configPost);
  } else {
    tinymce.init(configMobile);
  }
}

function setupCustomIco(editor) {
    editor.addButton('private', {
      text: 'Prv',
      icon: false,
      onclick: function() {
        editor.windowManager.open({
          title: 'Message privé',
          url: BASE_PATH + '/editor/tagPrivate/' + CAMPAGNE_ID,
          height: "280",
          buttons: [{
            text: 'OK',
            classes: 'widget btn primary first abs-layout-item',
            disabled: false,
            onclick: function(e){

              var find_src = BASE_PATH + '/editor/tagPrivate/' + CAMPAGNE_ID;
              var items = [];
              var val = $("iframe[src='" + find_src + "']").contents().find("select option:selected").each(function() {
                items.push($(this).val());
              });
              editor.execCommand( 'mceInsertContent', 0, "[private=" + items.join(',') + "]" + editor.selection.getContent() + "[/private]" );
              editor.windowManager.close();
            }
          }, {
            text: 'Cancel',
            onclick: 'close'
          }]
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
    editor.addButton('popup', {
      text: 'Pop',
      icon: false,
      onclick: function() {
        editor.windowManager.open({
          title: 'Informations Popup',
          body: [{
            type: 'textbox',
            name: 'title',
            label: 'Titre :'
          },
                 {
                   type: 'textbox',
                   name: 'link',
                   label: 'Lien affiché :'
                 }],
          onsubmit: function(e) {
            var content = tinyMCE.activeEditor.selection.getContent();
            editor.insertContent('[popup=' + e.data.title + ',' + e.data.link + ']' + content + '[/popup]');
          }
        });
      }
    });


    editor.addButton('perso', {
      text: 'PNJ',
      icon: false,
      onclick: function() {
        editor.windowManager.open({
          title: 'Lien vers fiche PNJ',
          url: BASE_PATH + '/editor/tagPerso/' + CAMPAGNE_ID,
          height: "280",
          buttons: [{
            text: 'OK',
            classes: 'widget btn primary first abs-layout-item',
            disabled: false,
            onclick: function(e){

              var find_src = BASE_PATH + '/editor/tagPerso/' + CAMPAGNE_ID;
              var val = $("iframe[src='" + find_src + "']").contents().find("select option:selected").val();
              var content = editor.selection.getContent() ? editor.selection.getContent(): val;
              editor.execCommand( 'mceInsertContent', 0, "[pnj=" + val + "]" + content + "[/pnj]" );
              editor.windowManager.close();
            }
          }, {
            text: 'Cancel',
            onclick: 'close'
          }]
        });
      }
    });


    editor.addButton('carte', {
      text: 'CARTE',
      icon: false,
      onclick: function() {
        editor.windowManager.open({
          title: 'Lien vers une carte',
          url: BASE_PATH + '/editor/tagCarte/' + CAMPAGNE_ID,
          height: "280",
          buttons: [{
            text: 'OK',
            classes: 'widget btn primary first abs-layout-item',
            disabled: false,
            onclick: function(e){

              var find_src = BASE_PATH + '/editor/tagCarte/' + CAMPAGNE_ID;
              var selection = $("iframe[src='" + find_src + "']").contents().find("select option:selected");
              var val = selection.val();
              var content = editor.selection.getContent() ? editor.selection.getContent(): selection.text();
              editor.execCommand( 'mceInsertContent', 0, "[carte=" + val + "]" + content + "[/carte]" );
              editor.windowManager.close();
            }
          }, {
            text: 'Cancel',
            onclick: 'close'
          }]
        });
      }
    });

    editor.addShortcut('ctrl+1', 'Dialogue Format', function() {
     editor.formatter.toggle('dialogue'); 
    });
    editor.addShortcut('ctrl+2', 'Dialogue Format', function() {
     editor.formatter.toggle('pensee'); 
    });
    editor.addShortcut('ctrl+3', 'Dialogue Format', function() {
     editor.formatter.toggle('rp1'); 
    });
    editor.addShortcut('ctrl+4', 'Dialogue Format', function() {
     editor.formatter.toggle('rp2'); 
    });
  }
