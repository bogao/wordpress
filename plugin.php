<?php
/*
Plugin Name: 高博的世界
Description: 高博的世界专用WordPress插件
Author: 高博
Version: 1.0.0
Author URI: http://gao.bo
*/
/* Start Adding Functions Below this Line */

// Register and load the widgets
function load_widgets() {
    register_widget( 'links_displayer' );
    register_widget( 'wpmwc_displayer' );
}
add_action( 'widgets_init', 'load_widgets' );
?>

<?php
class links_displayer extends WP_Widget {

    function __construct() {
        parent::__construct(
            'links_displayer',
            '随机链接',
            array ( 'description' => '从选定的链接分类目录中随机选取一个，显示其中的所有链接。' )
        );
    }

    function form( $instance )
    {
        if( $instance ) {
            $select = $instance['select'];
            $specified = $instance['specified'];
            $title = ( ! empty( $instance['title'] ) ) ? strip_tags( $instance['title'] ) : '';
        }
        else{
            $select ='';
            $specified = '';
            $title = '友情链接';
        }
        $bmcats = get_terms('link_category');
        if( $bmcats )
        {
            printf(
                '<select multiple="multiple" name="%s[]" id="%s" class="widefat" size="%d">',
                $this->get_field_name('select'),
                $this->get_field_id('select'),
                count($bmcats) + 1
            );
            foreach( $bmcats as $bmcat )
            {
                printf(
                    '<option value="%s" class="hot-topic" %s style="margin-bottom:3px;">%s</option>',
                    $bmcat->term_id,
                    in_array( $bmcat->term_id, $select) ? 'selected="selected"' : '',
                    $bmcat->name
                );
            }
            echo '</select>';
        }
        else
            echo '没有任何链接分类目录';
        echo '<input type="checkbox" id="' . $this->get_field_id('specified') .  '" name="' . $this->get_field_name('specified')
            . (($specified == '') ? '">' : '" checked>');
        echo '<label for="' . $this->get_field_id('title') . '">指定统一标题</label>';
        echo '<input type="text" class="widefat" id="' . $this->get_field_id('title') .  '" name="' . $this->get_field_name('title')
            . '" value="' . esc_attr( $title ) . '">';
    }

    function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        $instance['select'] = esc_sql( $new_instance['select'] );
        $instance['specified'] = $new_instance['specified'];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }

    function widget( $args, $instance ) {
        if ((!empty($instance['specified'])) && (!empty($instance['title']))){
            $title = apply_filters( 'widget_title', $instance['title'] );
        } else {
            $pick = intval($instance['select'][array_rand($instance['select'])]);
            $bmcats = get_terms('link_category');
            foreach ($bmcats as $bmcat){
                if ($pick == $bmcat->term_id){
                    $title = $bmcat->name;
                    break;
                }
            }
        }
        echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'] . '<ul>';
        foreach (get_bookmarks( array("category" => $pick)) as $bmitem){
            echo '<li><a href="' . $bmitem->link_url
                . ((empty($bmitem->link_target)) ? '"' : ('" target="' . $bmitem->link_target))
                . ((empty($bmitem->link_description)) ? '">' : ('" title="' . $bmitem->link_description . '">'))
                . $bmitem->link_name . '</a></li>';
        }
        echo '</ul>' . $args['after_widget'];
    }
}

class wpmwc_displayer extends WP_Widget {

    function __construct() {
        parent::__construct(
            'wpmwc_displayer',
            '博客和维基',
            array ( 'description' => '随机显示指定的WordPress博客和MediaWiki维基中的内容。' )
        );
    }

    function form( $instance ) {
    }

    function update( $new_instance, $old_instance ) {
    }

    function widget( $args, $instance ) {

    }
}

?>
