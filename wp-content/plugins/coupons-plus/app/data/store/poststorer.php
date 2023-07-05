<?php

namespace CouponsPlus\App\Data\Store;

Abstract class PostStorer extends Storer
{
    protected $post;
    
    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
    }

    public function updateMeta(string $key, /*mixed*/ $value)
    {
        // please note: $value is already sanitized by the calling code.
        update_post_meta($this->post->ID, $key, $value);
    }
}