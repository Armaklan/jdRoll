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

var style_formats = [
  formats.dialogue,
  formats.pensee,
  formats.rp1,
  formats.rp2,
  formats.rp3,
  formats.t1,
  formats.t2,
  formats.t3,
  formats.blockquote
];

var fontFormat = "Standard=Helvetica Neue, Helvetica, Arial, sans-serif;" +
    "Police Lcd=LiquidCrystal;"+
    "Police Runique=Runes;"+
    "Police Elfique=Tengwar";

var configBase = {
  plugins: [
    "link image lists hr",
    "table fullscreen",
    "emoticons code"
  ],
  toolbar_mode: "sliding",
  menubar: false,
  contextmenu: false,
  autoresize: true,
  mobile: {
    min_height: 300,
    toolbar_mode: "sliding",
    toolbar: "fullscreen | styleselect removeformat | bold italic | link image | hr | private hide popup perso perso2 carte"
  },
  content_css : BASE_PATH + "/css/main.css",
  browser_spellcheck: true,
  convert_urls: false,
  toolbar: "undo redo | styleselect fontselect removeformat | bold italic forecolor | link image | fullscreen | emoticons private hide popup perso perso2 carte | hr | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table code",
  formats: formats,
  style_formats: style_formats,
  autosave_ask_before_unload: false,
  font_formats: fontFormat,
  setup: setupCustomIco
};

var configPost = jQuery.extend(true, {}, configBase);
configPost.min_height = 400;
configPost.selector = ".wysiwyg";

tinymce.init(configPost);

function setupCustomIco(editor) {
    editor.ui.registry.addButton('private', {
      text: 'Prv',
      onAction: function() {
        editor.windowManager.openUrl({
          title: 'Message privé',
          url: BASE_PATH + '/editor/tagPrivate/' + CAMPAGNE_ID,
          height: 280,
          buttons: [{
            type: "custom",
            text: 'OK',
            classes: 'widget btn primary first abs-layout-item',
            disabled: false
          }, {
            type: "cancel",
            text: 'Cancel',
            onAction: 'close'
          }],
          onAction: function(e){

            var find_src = BASE_PATH + '/editor/tagPrivate/' + CAMPAGNE_ID;
            var items = [];
            var val = $("iframe[src='" + find_src + "']").contents().find("select option:selected").each(function() {
              items.push($(this).val());
            });
            editor.execCommand( 'mceInsertContent', 0, "[private=" + items.join(',') + "]" + editor.selection.getContent() + "[/private]" );
            editor.windowManager.close();
          }
        });
      }
    });
    editor.ui.registry.addButton('hide', {
      text: 'Hid',
      onAction: function() {
        var content = tinyMCE.activeEditor.selection.getContent();
        editor.insertContent('[hide]' + content + '[/hide]');
      }
    });
    editor.ui.registry.addButton('popup', {
      text: 'Pop',
      onAction: function() {
        editor.windowManager.open({
          title: 'Informations Popup',
          body: {
            type: 'panel',
            items: [{
              type: 'input',
              name: 'title',
              label: 'Titre :'
            },
            {
              type: 'input',
              name: 'link',
              label: 'Lien affiché :'
            }]
          },
          buttons: [{
            type: "submit",
            text: 'OK',
            classes: 'widget btn primary first abs-layout-item',
            disabled: false
          }, {
            type: "cancel",
            text: 'Cancel',
            onAction: 'close'
          }],
          onSubmit: function(e) {
            var data = e.getData();
            var content = tinyMCE.activeEditor.selection.getContent();
            editor.insertContent('[popup=' + data.title + ',' + data.link + ']' + content + '[/popup]');
            editor.windowManager.close();
          }
        });
      }
    });


    editor.ui.registry.addButton('perso', {
      text: 'PNJ',
      onAction: function() {
        editor.windowManager.openUrl({
          title: 'Lien vers fiche PNJ',
          url: BASE_PATH + '/editor/tagPerso/' + CAMPAGNE_ID,
          height: 280,
          buttons: [{
            type: "custom",
            text: 'OK',
            classes: 'widget btn primary first abs-layout-item',
            disabled: false,
          }, {
            type: "cancel",
            text: 'Cancel',
            onAction: 'close'
          }],
          onAction: function(e){

            var find_src = BASE_PATH + '/editor/tagPerso/' + CAMPAGNE_ID;
            var val = $("iframe[src='" + find_src + "']").contents().find("select option:selected").val();
            var content = editor.selection.getContent() ? editor.selection.getContent(): val;
            editor.execCommand( 'mceInsertContent', 0, "[pnj=" + val + "]" + content + "[/pnj]" );
            editor.windowManager.close();
          }
        });
      }
    });


    editor.ui.registry.addButton('carte', {
      text: 'CARTE',
      onAction: function() {
        editor.windowManager.openUrl({
          title: 'Lien vers une carte',
          url: BASE_PATH + '/editor/tagCarte/' + CAMPAGNE_ID,
          height: 280,
          buttons: [{
            type: "custom",
            text: 'OK',
            classes: 'widget btn primary first abs-layout-item',
            disabled: false
          }, {
            type: "cancel",
            text: 'Cancel',
            onAction: 'close'
          }],
          onAction: function(e){

            var find_src = BASE_PATH + '/editor/tagCarte/' + CAMPAGNE_ID;
            var selection = $("iframe[src='" + find_src + "']").contents().find("select option:selected");
            var val = selection.val();
            var content = editor.selection.getContent() ? editor.selection.getContent(): selection.text();
            editor.execCommand( 'mceInsertContent', 0, "[carte=" + val + "]" + content + "[/carte]" );
            editor.windowManager.close();
          }
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
