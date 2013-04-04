/**
 * @author Dongsheng Cai <dongsheng@moodle.com>
 */

(function() {
    var each = tinymce.each;

    tinymce.PluginManager.requireLangPack('moodlemedia');

    tinymce.create('tinymce.plugins.MultiLangPlugin', {
        init : function(ed, url) {
            var t = this;

            t.editor = ed;
            t.url = url;

            // Register commands.
            ed.addCommand('mceMultilang', function() {
                ed.windowManager.open({
                    file : url + '/multilang.htm',
                    width : 480 + parseInt(ed.getLang('media.delta_width', 0)),
                    height : 480 + parseInt(ed.getLang('media.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register buttons.
            ed.addButton('multilang', {
                    title : 'multilang.desc',
                    image : url + '/img/icon.png',
                    cmd : 'mceMultilang'});

        },

        _parse : function(s) {
            return tinymce.util.JSON.parse('{' + s + '}');
        },

        getInfo : function() {
            return {
                longname : 'Multi Language Selector',
                author : 'Dongsheng Cai <dongsheng@moodle.com>',
                version : "1.0"
            };
        }

    });

    // Register plugin.
    tinymce.PluginManager.add('multilang', tinymce.plugins.MultiLangPlugin);
})();
