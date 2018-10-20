<?php foreach($svrInfo['net'] as $key=>$value){ ?>
    <tr>
        <td class="text-center"><?php echo $key; ?></td>
        <td class="text-center">
            <span id="<?php echo $value['name']; ?>_rxbytes"><?php echo $value['rxbytes']; ?></span>
            (<span id="<?php echo $value['name']; ?>_rxspeed" class="stxt"></span>)
        </td>
        <td class="text-center">
            <span id="<?php echo $value['name']; ?>_txbytes"><?php echo $value['txbytes']; ?></span>
            (<span id="<?php echo $value['name']; ?>_txspeed" class="stxt"></span>)
        </td>
        <td class="text-center">
            <span id="<?php echo $value['name']; ?>_errors"><?php echo $value['errors']; ?></span> /
            <span id="<?php echo $value['name']; ?>_drops"><?php echo $value['drops']; ?></span>
        </td>
    </tr>
<?php } ?>