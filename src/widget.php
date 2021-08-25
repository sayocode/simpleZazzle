<?php

// 商品一覧をウィジェットに出力できるようにする
class ScszWidget extends WP_Widget{
	/**
	 * Widgetを登録する
	 */
	function __construct() {
		parent::__construct(
			'scszWidget',		// Base ID
			'SC Simple Zazzle',	//ウィジェット名
			array('description' => __('Output a list of "SC Simle Zazzle" products you set.', 'sc-simple-zazzle'))	//ウィジェットの概要
			);
	}

	//ウィジェットの表示
	public function widget($args, $instance){
		echo $args['before_widget'];
		if (!empty($instance['scsz_widget_title'])){
			echo $args['before_title'] . $instance['scsz_widget_title'] . $args['after_title'];
		}
		if (!empty($instance['scsz_widget_scid'])){
			echo sc_echo_item_list(array('id' => $instance['scsz_widget_scid']));
		}
		echo $args['after_widget'];
	}

	public function form( $instance ){

		if(empty($instance)){
			$instance = array('scsz_widget_title' =>'My items', 'scsz_widget_scid' => 1);
		}

		// タイトル
		$scsz_widgrt_title = $instance['scsz_widget_title'];
		$scsz_widgrt_title_name = $this->get_field_name('scsz_widget_title');
		$scsz_widgrt_title_id = $this->get_field_id('scsz_widget_title');
		echo '<p><label for="'.$scsz_widgrt_title_id.'">'.__('Title').'</label>';
		echo '<input class="widefat" id="'.$scsz_widgrt_title_id.'" name="'.$scsz_widgrt_title_name.'" type="text" value="'.$scsz_widgrt_title.'"></p>';

		// sc_simple_zazzle_tableからデータを取得
		global $wpdb;
		$scsz_table_name = $wpdb->prefix . "sc_simple_zazzle_table";
		$scsz_all_feed_settings = $wpdb->get_results("SELECT * FROM " . $scsz_table_name);

		// アイテム
		$scsz_widgrt_scid_name = $this->get_field_name('scsz_widget_scid');
		$scsz_widgrt_scid_id = $this->get_field_id('scsz_widget_scid');
		echo '<p><label for="'.$scsz_widgrt_scid_id.'">'.__('Select item', 'sc-simple-zazzle').'</label>';
		echo '<select id="'.$scsz_widgrt_scid_id.'" name="'.$scsz_widgrt_scid_name.'" style="width:100%;">';
		foreach ($scsz_all_feed_settings as $scsz_item){
			$scsz_widget_scid_select = $instance['scsz_widget_scid'] == $scsz_item->scid ? 'selected' : '';
			echo '<option value="'.$scsz_item->scid.'" '.$scsz_widget_scid_select.'>'.$scsz_item->title.'</option>';
		}
		echo '</select></p>';
	}
	
	function update($new_instance, $old_instance) {
		return $new_instance;
	}
}

add_action(
	'widgets_init',
	function(){
		register_widget('ScszWidget'); //ウィジェットのクラス名を記述
	}
);