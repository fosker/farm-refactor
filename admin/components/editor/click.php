<?php

/**
 *
 * basic preset returns the basic toolbar configuration set for CKEditor.
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */
return [
    'height' => 200,
    'toolbarGroups' => [
        ['name' => 'undo'],
        ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],

    ],
    'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
    'removePlugins' => 'elementspath',
    'resize_enabled' => false
];
