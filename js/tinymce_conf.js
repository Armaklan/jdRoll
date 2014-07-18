var selected = [];

var formats = [{
        title: 'Dialogue',
        inline: 'span',
        classes: 'dialogue'
    }, {
        title: 'Pensée',
        inline: 'span',
        classes: 'pensee'
    }, {
        title: 'RP1',
        inline: 'span',
        classes: 'rp1'
    },  {
        title: 'RP2',
        inline: 'span',
        classes: 'rp2'
    }, {
        title: 'Hrp',
        inline: 'span',
        classes: 'hrp'
    }, {
        title: 'Titre 1',
        block: 'h1'
    }, {
        title: 'Titre 2',
        block: 'h2'
    }, {
        title: 'Titre 3',
        block: 'h3'
    }, {
        title: 'Citation',
        block: 'blockquote'
    }];

var configBase = {
    plugins: [
        "link image lists preview hr insertdatetime",
        "table template textcolor fullscreen",
        "emoticons code spellchecker"
    ],
    content_css : BASE_PATH + "/css/main.css",
    browser_spellcheck: true,
    convert_urls: false,
    spellchecker_languages: "+French=fr",
    spellchecker_rpc_url: "/tinymce/plugins/spellchecker/rpc.php",
    toolbar: "cut copy paste | styleselect removeformat | bold italic forecolor | alignleft aligncenter alignright alignjustify | hr | bullist numlist outdent indent | link image | preview fullscreen | emoticons private hide popup",
    style_formats: formats,
    autosave_ask_before_unload: false,
    setup: function(editor) {
        editor.addButton('private', {
            text: 'Prv',
            icon: false,
            onclick: function() {
                editor.windowManager.open({
                    title: 'Message privée',
                    body: [{
                        type: 'listbox',
                        name: 'users',
                        multiple: 'true',
                        size: 'large',
                        width: '200',
                        values: specificValue,
                        onselect: function(e) {
                            if (selected.indexOf(e.control.value()) == -1) {
                                selected.push(e.control.value());
                            } else {
                                selected.splice(e.control.value());
                            }
                        }
                    }],
                    onsubmit: function(e) {
                        var content = tinyMCE.activeEditor.selection.getContent();
                        editor.insertContent('[private=' + selected.join() + ']' + content + '[/private]');
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
    }
};

var configPost = jQuery.extend(true, {}, configBase);
configPost.min_height = 160;
configPost.selector = ".wysiwyg";

var configMobile = jQuery.extend(true, {}, configBase);
configMobile.plugins = [
    "link image lists preview hr insertdatetime",
    "table template textcolor fullscreen",
    "emoticons code"
];
configMobile.toolbar = "paste | styleselect removeformat | bold italic | hr | link image | code";
configMobile.menubar = false;
configMobile.min_height = 400;
configMobile.selector = ".wysiwyg";

var configNote = jQuery.extend(true, {}, configBase);
configNote.toolbar = "cut copy paste | styleselect removeformat | bold italic forecolor | alignleft aligncenter alignright alignjustify | hr | bullist numlist outdent indent | link image | preview fullscreen | emoticons",
configNote.min_height = 160;
configNote.selector = ".note-wysiwyg";

var configNoteMobile = jQuery.extend(true, {}, configNote);
configNoteMobile.min_height = 160;
configNoteMobile.width = 300;
configNoteMobile.selector = ".note-wysiwyg";

if (navigator.userAgent.indexOf("IE") != -1) {
    tinymce.init(configPost);
    tinymce.init(configNote);
} else {
    if (window.matchMedia("(min-width: 600px)").matches) {
        tinymce.init(configPost);
        tinymce.init(configNote);
    } else {
        tinymce.init(configMobile);
        tinymce.init(configNoteMobile);
    }
}
