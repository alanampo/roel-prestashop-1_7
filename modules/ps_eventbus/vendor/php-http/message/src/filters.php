<?php

namespace ps_eventbus_v3_0_7;

// Register chunk filter if not found
if (!\array_key_exists('chunk', \stream_get_filters())) {
    \stream_filter_register('chunk', 'ps_eventbus_v3_0_7\\Http\\Message\\Encoding\\Filter\\Chunk');
}
