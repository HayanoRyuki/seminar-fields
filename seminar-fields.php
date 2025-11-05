<?php
/*
Plugin Name: Seminar Fields
Description: 講習会ページ専用のカスタムフィールド
Version: 1.0
Author: Media-Confidence
*/

// メタボックス追加
function seminar_add_custom_fields() {
    global $post;

    // テンプレートが "page-seminar.php" の場合のみ実行
    $template = get_post_meta( $post->ID, '_wp_page_template', true );
    if ( $template !== 'page-seminar.php' ) {
        return; // 他ページでは非表示
    }

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

// メタボックスのHTML
function seminar_fields_html($post) {
    $fields = [
        'basic_date_1' => '基礎編 日程①',
        'basic_date_2' => '基礎編 日程②',
        'advanced_date_1' => '応用編 日程①',
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
}

// 保存処理
function seminar_save_custom_fields($post_id) {
    $keys = [
        'basic_date_1',
        'basic_date_2',
        'advanced_date_1',
        'curriculum_title',
        'curriculum_text',
        'curriculum_basic',
        'curriculum_advanced'
    ];
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }
}
add_action('save_post', 'seminar_save_custom_fields');
