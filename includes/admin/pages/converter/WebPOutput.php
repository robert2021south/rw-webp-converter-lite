<?php
namespace RobertWP\WebPConverterLite\Admin\Pages\Converter;

use DOMDocument;
use DOMException;
use RobertWP\WebPConverterLite\Traits\Singleton;

class WebPOutput
{
    use Singleton;

    public function init(): void
    {
        // 内容中的 <img>
        add_filter('the_content', [$this, 'replace_img_with_picture'], 99);
        // 文章缩略图
        add_filter('post_thumbnail_html', [$this, 'replace_img_with_picture'], 99, 5);
    }

    /**
     * 替换内容中的 <img> 为 <picture>，支持 WebP、延迟加载和响应式
     * @throws DOMException
     */
    public function replace_img_with_picture($content, $post_id = null)
    {
        if (empty($content)) return $content;

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $imgs = $dom->getElementsByTagName('img');

        foreach ($imgs as $img) {
            $src = $img->getAttribute('src');
            $attachment_id = attachment_url_to_postid($src);
            if (!$attachment_id) continue;

            $meta = wp_get_attachment_metadata($attachment_id);
            if (empty($meta['webp'])) continue;

            $upload_dir = wp_upload_dir();
            $webp_file  = $meta['webp']['file'];
            $webp_url   = trailingslashit($upload_dir['baseurl']) . $webp_file;

            // 构建 WebP srcset 和 sizes
            $srcset_webp = [];
            $widths = [];
            if (!empty($meta['sizes'])) {
                foreach ($meta['sizes'] as $size => $data) {
                    if (!empty($data['file']) && !empty($data['width'])) {
                        $file_webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $data['file']);
                        $srcset_webp[] = trailingslashit($upload_dir['baseurl']) . $file_webp . ' ' . $data['width'] . 'w';
                        $widths[] = $data['width'];
                    }
                }
            }
            // 原图
            $srcset_webp[] = $webp_url . ' ' . $meta['width'] . 'w';
            $widths[] = $meta['width'];

            // sizes 属性，按屏幕宽度自动映射
            $sizes_attr = implode(', ', array_map(fn($w) => "(max-width: {$w}px) {$w}px", $widths));
            $sizes_attr .= ', 100vw'; // fallback

            // 构造 <picture>
            $picture = $dom->createElement('picture');

            // WebP source
            $source = $dom->createElement('source');
            $source->setAttribute('type', 'image/webp');
            $source->setAttribute('srcset', implode(', ', $srcset_webp));
            $source->setAttribute('sizes', $sizes_attr);
            $picture->appendChild($source);

            // 原 img fallback，添加延迟加载
            $new_img = $img->cloneNode(true);
            $new_img->setAttribute('loading', 'lazy');
            $picture->appendChild($new_img);

            // 替换原 img
            $img->parentNode->replaceChild($picture, $img);
        }

        return $dom->saveHTML();
    }
}
