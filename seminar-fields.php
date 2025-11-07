<?php
/*
Plugin Name: Seminar Fields
Description: 講習会ページ専用カスタムフィールド（基礎＋応用リピーター）
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

    // 基礎編
    echo '<h3>基礎編 日程（複数追加可）</h3>';
    echo '<div id="basic-repeater">';
    foreach ($basic_dates as $val) {
        echo '<div class="repeater-item">';
        echo '<input type="text" name="basic_dates[]" value="' . esc_attr($val) . '" class="widefat" />';
        echo '<button type="button" class="button remove-item">× 削除</button>';
        echo '</div>';
    }
    echo '<button type="button" class="button add-basic">＋ 追加</button>';
    echo '</div>';

    echo '<hr style="margin:20px 0;">';

    // 応用編
    echo '<h3>応用編 日程（複数追加可）</h3>';
    echo '<div id="adv-repeater">';
    foreach ($advanced_dates as $val) {
        echo '<div class="repeater-item">';
        echo '<input type="text" name="advanced_dates[]" value="' . esc_attr($val) . '" class="widefat" />';
        echo '<button type="button" class="button remove-item">× 削除</button>';
        echo '</div>';
    }
    echo '<button type="button" class="button add-adv">＋ 追加</button>';
    echo '</div>';

    // カリキュラムなど既存項目も残す
    $fields = [
        'curriculum_title' => 'カリキュラム タイトル',
        'curriculum_text' => 'カリキュラム 説明文',
        'curriculum_basic' => 'カリキュラム（基礎編）',
        'curriculum_advanced' => 'カリキュラム（応用編）'
    ];

    echo '<table class="form-table">';
    foreach ($fields as $key => $label) {
        $value = esc_attr(get_post_meta($post->ID, $key, true));
        echo "<tr><th><label for='{$key}'>{$label}</label></th>";
        echo "<td><input type='text' id='{$key}' name='{$key}' value='{$value}' class='widefat'></td></tr>";
    }
    echo '</table>';

    // 簡易JS（管理画面用リピーター追加・削除）
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // 追加ボタン
        document.querySelectorAll('.add-basic, .add-adv').forEach(function(btn){
            btn.addEventListener('click', function(){
                const container = btn.closest('div');
                const inputDiv = document.createElement('div');
                inputDiv.className = 'repeater-item';

                const input = document.createElement('input');
                input.type = 'text';
                input.name = btn.classList.contains('add-basic') ? 'basic_dates[]' : 'advanced_dates[]';
                input.className = 'widefat';
                inputDiv.appendChild(input);

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'button remove-item';
                removeBtn.textContent = '× 削除';
                inputDiv.appendChild(removeBtn);

                container.insertBefore(inputDiv, btn);
            });
        });

        // 削除ボタン
        document.addEventListener('click', function(e){
            if(e.target && e.target.classList.contains('remove-item')){
                e.preventDefault();
                const parent = e.target.closest('.repeater-item');
                if(parent) parent.remove();
            }
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
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }
}
add_action('save_post', 'seminar_save_custom_fields');
