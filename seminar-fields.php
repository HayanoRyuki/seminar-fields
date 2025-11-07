<?php
/*
Plugin Name: Seminar Fields
Description: 講習会ページ専用カスタムフィールド（基礎＋応用リピーター＋カリキュラム HTML編集）
Version: 1.0.3
Author: Media-Confidence
*/

// ==============================
// メタボックス追加
// ==============================
function seminar_add_custom_fields() {
    global $post;
    if (!isset($post)) return;
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    if ($template !== 'page-seminar.php') return;

    add_meta_box(
        'seminar_fields',
        '講習会ページ設定',
        'seminar_fields_html',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'seminar_add_custom_fields');

// ==============================
// メタボックスHTML
// ==============================
function seminar_fields_html($post) {
    $basic_dates = get_post_meta($post->ID, 'basic_dates', true);
    $advanced_dates = get_post_meta($post->ID, 'advanced_dates', true);
    if (!is_array($basic_dates)) $basic_dates = [];
    if (!is_array($advanced_dates)) $advanced_dates = [];

    // 基礎編日程
    echo '<h3>基礎編 日程（複数追加可）</h3>';
    echo '<div id="basic-repeater">';
    foreach ($basic_dates as $val) {
        echo '<input type="text" name="basic_dates[]" value="' . esc_attr($val) . '" class="widefat" />';
    }
    echo '<button type="button" class="button add-basic">＋ 追加</button>';
    echo '</div>';

    echo '<hr style="margin:20px 0;">';

    // 応用編日程
    echo '<h3>応用編 日程（複数追加可）</h3>';
    echo '<div id="adv-repeater">';
    foreach ($advanced_dates as $val) {
        echo '<input type="text" name="advanced_dates[]" value="' . esc_attr($val) . '" class="widefat" />';
    }
    echo '<button type="button" class="button add-adv">＋ 追加</button>';
    echo '</div>';

    echo '<hr style="margin:20px 0;">';

    // カリキュラム（HTML編集可能）
    $cur_basic = get_post_meta($post->ID, 'curriculum_basic', true);
    $cur_adv   = get_post_meta($post->ID, 'curriculum_advanced', true);
    $cur_title = get_post_meta($post->ID, 'curriculum_title', true);
    $cur_text  = get_post_meta($post->ID, 'curriculum_text', true);

    echo '<h3>カリキュラムタイトル</h3>';
    echo '<input type="text" name="curriculum_title" value="' . esc_attr($cur_title) . '" class="widefat" />';

    echo '<h3>カリキュラム説明文</h3>';
    echo '<textarea name="curriculum_text" class="widefat" rows="3">' . esc_textarea($cur_text) . '</textarea>';

    echo '<h3>カリキュラム（基礎編）</h3>';
    wp_editor(
        $cur_basic,
        'curriculum_basic',
        [
            'textarea_name' => 'curriculum_basic',
            'textarea_rows' => 15,
            'media_buttons' => false,
            'teeny' => true,
        ]
    );

    echo '<h3>カリキュラム（応用編）</h3>';
    wp_editor(
        $cur_adv,
        'curriculum_advanced',
        [
            'textarea_name' => 'curriculum_advanced',
            'textarea_rows' => 15,
            'media_buttons' => false,
            'teeny' => true,
        ]
    );

    // 管理画面用JS（リピーター追加）
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.add-basic, .add-adv').forEach(function(btn){
            btn.addEventListener('click', function(){
                const container = btn.closest('div');
                const input = document.createElement('input');
                input.type = 'text';
                input.name = btn.classList.contains('add-basic') ? 'basic_dates[]' : 'advanced_dates[]';
                input.className = 'widefat';
                container.insertBefore(input, btn);
            });
        });
    });
    </script>
    <?php
}

// ==============================
// 保存処理
// ==============================
function seminar_save_custom_fields($post_id) {
    // 基礎編
    if (isset($_POST['basic_dates'])) {
        $dates = array_filter(array_map('sanitize_text_field', $_POST['basic_dates']));
        update_post_meta($post_id, 'basic_dates', $dates);
    }
    // 応用編
    if (isset($_POST['advanced_dates'])) {
        $dates = array_filter(array_map('sanitize_text_field', $_POST['advanced_dates']));
        update_post_meta($post_id, 'advanced_dates', $dates);
    }
    // カリキュラム
    $keys = ['curriculum_title','curriculum_text','curriculum_basic','curriculum_advanced'];
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            if (in_array($key, ['curriculum_basic','curriculum_advanced'])) {
                // HTMLを許可
                update_post_meta($post_id, $key, $_POST[$key]);
            } else {
                update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
            }
        }
    }
}
add_action('save_post', 'seminar_save_custom_fields');
