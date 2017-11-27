<input type="hidden" name="uwopeoplemeta_noncename" id="uwopeoplemeta_noncename" value="<?php echo wp_create_nonce( plugin_basename(__FILE__)); ?>" />
<table>
    <tr>
        <td>ePanther ID: </td>
        <td><input type="text" name="uwopeople_epantherid" value=""/></td>
        <td>
            <div id="button-ods" class="button button-primary"><?php echo apply_filters('uwopeople-edit-button-title', 'Load ODS'); ?></div>
        </td>
    </tr>
</table>