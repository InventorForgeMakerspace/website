<?php
if (!class_exists('Timber')){
    echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
}
$context = Timber::get_context();
$context['post'] = Timber::get_post();

$context['contributors'] = get_users( array(
    'connected_type' => 'project_contributors',
    'connected_items' => $post
) );

Timber::render( 'project.twig', $context );