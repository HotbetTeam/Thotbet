<div class="OPeration">
    <a class="pull-right btn btn-blue btn-large" href="<?= URL ?>operator/form" data-plugins="dialog"> <i class="icon-plus"></i> รายการใหม่</a>
    <h1 class="heading">
        บันทึกข้อมูล Operator

    </h1>

    <table class="tableOP">
        <tr>
            <th>ผู้บันทึก</th>
            <th> ลูกค้า</th>
            <th>วันที่บันทึก</th>
            <th><i class="icon-cog"></i></th>
        </tr>

        <?php
        foreach ($this->item AS $key => $item) {
            ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['op_customer']; ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($item['op_date'])); ?></td>
                <td>
                    <a class="btn btn-facebook"> <i class="icon-pencil"></i> </a>
                    <a class="btn btn-google"> <i class="icon-remove"></i> </a>
                </td>
            </tr>

            <?php
        }
        ?>
    </table>
</div>