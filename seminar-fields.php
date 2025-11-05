<?php
/*
Plugin Name: Seminar Fields
Description: 講習会LP用の入力項目（ACF不要）
Version: 1.0
Author: Media Confidence
*/

add_action('add_meta_boxes', function() {
  add_meta_box('seminar_fields', '講習会ページ設定', 'seminar_fields_callback', 'page', 'normal', 'high');
});

function seminar_fields_callback($post) {
  $values = get_post_meta($post->ID);
  ?>
  <h4>■ 開催日程</h4>
  <table class="form-table">
    <tr><th>基礎編 日程①</th><td><input type="text" name="basic_date_1" value="<?php echo esc_attr($values['basic_date_1'][0] ?? ''); ?>" class="widefat" /></td></tr>
    <tr><th>基礎編 日程②</th><td><input type="text" name="basic_date_2" value="<?php echo esc_attr($values['basic_date_2'][0] ?? ''); ?>" class="widefat" /></td></tr>
    <tr><th>応用編 日程①</th><td><input type="text" name="advanced_date_1" value="<?php echo esc_attr($values['advanced_date_1'][0] ?? ''); ?>" class="widefat" /></td></tr>
  </table>

  <h4>■ カリキュラム</h4>
  <table class="form-table">
    <tr><th>セクションタイトル</th><td><input type="text" name="curriculum_title" value="<?php echo esc_attr($values['curriculum_title'][0] ?? 'カリキュラム'); ?>" class="widefat" /></td></tr>
    <tr><th>説明文</th><td><textarea name="curriculum_text" rows="3" class="widefat"><?php echo esc_textarea($values['curriculum_text'][0] ?? ''); ?></textarea></td></tr>
    <tr><th>基礎編 内容</th><td><textarea name="curriculum_basic" rows="5" class="widefat"><?php echo esc_textarea($values['curriculum_basic'][0] ?? ''); ?></textarea></td></tr>
    <tr><th>応用編 内容</th><td><textarea name="curriculum_advanced" rows="5" class="widefat"><?php echo esc_textarea($values['curriculum_advanced'][0] ?? ''); ?></textarea></td></tr>
  </table>
  <?php
}

add_action('save_post', function($post_id) {
  $fields = ['basic_date_1','basic_date_2','advanced_date_1','curriculum_title','curriculum_text','curriculum_basic','curriculum_advanced'];
  foreach ($fields as $f) {
    if (isset($_POST[$f])) {
      update_post_meta($post_id, $f, sanitize_textarea_field($_POST[$f]));
    }
  }
});
