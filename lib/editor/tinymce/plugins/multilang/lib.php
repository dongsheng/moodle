<?php
defined('MOODLE_INTERNAL') || die();

class tinymce_multilang extends editor_tinymce_plugin {
    /** @var array list of buttons defined by this plugin */
    protected $buttons = array('multilang');

    protected function update_init_params(array &$params, context $context,
            array $options = null) {

        // Add button after emoticon button in advancedbuttons3.
        $added = $this->add_button_after($params, 3, 'multilang', 'moodlemedia', true);

        // Note: We know that the emoticon button has already been added, if it
        // exists, because I set the sort order higher for this. So, if no
        // emoticon, add after 'image'.
        if (!$added) {
            $this->add_button_after($params, 3, 'multilang', 'moodlemedia');
        }

        // Add JS file, which uses default name.
        $this->add_js_plugin($params);
    }

    protected function get_sort_order() {
        return 110;
    }
}
