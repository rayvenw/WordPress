<?php
/*
    Plugin Name: Count Plugin
    Description: This Plugin adds a word & character counter & read time to your site
    Version: 1.0
    Author: Rayven Walker 
    Text Domain: wcpdomain
    Domain Path: /languages
*/

class WordCountAndTimePlugin{
    function __construct(){
        add_action('admin_menu', array($this,'adminPage'));
        add_action('admin_init', array($this, 'settings'));
        add_filter('the_content', array($this, 'ifwrap'));
    }
    function ifwrap($content){
            if(is_main_query() AND is_single() AND 
            (
            get_option('wcp_wordcount', '1') OR 
            get_option('wcp_charactercount', '1') OR 
            get_option('wcp_readtime', '1')
            )) {
                return $this->createHTML($content);
            }
            return $content;
    }

    function createHTML($content){
        $html = '<h3>'. esc_html(get_option('wcp_headline', 'Post Statistics')) . '</h3><p>';

        // wordcount 
        if (get_option('wcp_wordcount', '1') OR get_option('wcp_readtime', '1') ){
            $wordcount = str_word_count(strip_tags($content));
        }

        if (get_option('wcp_wordcount', '1')){
            $html .= 'This post has' .$wordcount . ' words.<br>';
        }

        if (get_option('wcp_charactercount', '1')){
            $html .= 'This post has ' .strlen(strip_tags($content)) . ' words.<br>';
        }

        if (get_option('wcp_readtime', '1')){
            $html .= 'This post will take about ' . round($wordcount/225) . ' minute(s) to read.<br>';
        }
            $html .='</p>';
        if (get_option('wcp_location', '0') == '0'){
            return $html . $content;
        }
        return $content . $html;
    }

    function settings(){
        add_settings_section('wcp_first_section',null,null,'word-count-settings-page');
        /*Location */
        add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'word-count-settings-page', 'wcp_first_section');
        register_setting('wordcountplugin','wcp_location',array('sanitize_callback'=> array($this, 'sanitize_location'), 'default' => '0'));
        
        /*Headline Text */
        add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), 'word-count-settings-page', 'wcp_first_section');
        register_setting('wordcountplugin','wcp_headline',array('sanitize_callback'=> 'sanitize_text_field', 'default' => 'Post Statistics'));

        /* Word Count */
        add_settings_field('wcp_wordcount', 'Word Count', array($this, 'wordcountHTML'), 'word-count-settings-page', 'wcp_first_section');
        register_setting('wordcountplugin','wcp_wordcount',array('sanitize_callback'=> 'sanitize_text_field', 'default' => '1'));

        /* Character Count */
        add_settings_field('wcp_charactercount', 'Character Count', array($this, 'charactercountHTML'), 'word-count-settings-page', 'wcp_first_section');
        register_setting('wordcountplugin','wcp_charactercount',array('sanitize_callback'=> 'sanitize_text_field', 'default' => '1'));

        /* Read Time */
        add_settings_field('wcp_readtime', 'Read Time', array($this, 'readtimeHTML'), 'word-count-settings-page', 'wcp_first_section');
        register_setting('wordcountplugin','wcp_readtime',array('sanitize_callback'=> 'sanitize_text_field', 'default' => '1'));
    }

    function sanitize_location($input) {
        if ($input != '0' AND $input != '1'){
            add_settings_error('wcp_location','wcp_location_error','Display Location must be either beginning or end');
            return get_option('wcp_location');
        }
        return $input;
    }

    /* Read Time HTML */
    function readtimeHTML(){?>
        <input type="checkbox" name="wcp_readtime" value="1" <?php checked(get_option('wcp_readtime', '1')) ?>>
    
    <?php
        }
/* Character Count HTML */
    function charactercountHTML(){?>
        <input type="checkbox" name="wcp_charactercount" value="1" <?php checked(get_option('wcp_charactercount', '1')) ?>>
    
    <?php
        }
        /* Word Count HTML */
    function wordcountHTML(){?>
    <input type="checkbox" name="wcp_wordcount" value="1" <?php checked(get_option('wcp_wordcount', '1')) ?>>

<?php
    }


    /*Headline Text HTML*/
    function headlineHTML(){ ?>
    <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
    <?php }
    /*Location HTML */
    function locationHTML(){?>
   <select name="wcp_location" id="">
    <Option value="0" <?php selected(get_option('wcp_location'), '0')?>>Beginning of Post</Option>
   <Option value="1" <?php selected(get_option('wcp_location'), '1')?>>End of Post</Option></select>
   <?php }
    function adminPage(){
        add_options_page('Word Count Settings', 'Word Count','manage_options','word-count-settings-page',array($this,'ourHTML'));}
    
        function ourHTML(){ ?>
            <div class="wrap">
                <h1>Word Count Settings</h1>
                <form action="options.php" method="POST">
                    <?php
                        settings_fields('wordcountplugin');
                        do_settings_sections('word-count-settings-page');
                        submit_button();
                    ?>

                </form>
            </div>
            <?php }
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();







?>