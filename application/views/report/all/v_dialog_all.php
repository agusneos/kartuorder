<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<style type="text/css">
    #fm-dialog_all{
        margin:0;
        padding:20px 30px;
    }
    #dlg_btn-dialog_all{
        margin:0;
        padding:10px 100px;
    }
    .ftitle{
        font-size:14px;
        font-weight:bold;
        padding:5px 0;
        margin-bottom:10px;
        border-bottom:1px solid #ccc;
    }
    .fitem{
        margin-bottom:5px;
    }
    .fitem label{
        display:inline-block;
        width:100px;
    }
</style>
<!-- Form -->
    <form id="fm-dialog_all" method="post" novalidate buttons="#dlg_btn-dialog_all">
        <div class="fitem">
            <label for="type">LOT</label>
            <input id="lot" name="lot" class="easyui-textbox" required/>
        </div>
        <div class="fitem">
            <label for="type">SUB LOT</label>
            <input id="sublot" name="sublot" class="easyui-textbox" required />
        </div>
    </form>

<!-- Dialog Button -->
<div id="dlg_btn-dialog_all">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="cetak_all()">Proses</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')">Batal</a>
</div>

<script type="text/javascript">
    function cetak_all()
    {
        var isValid = $('#fm-dialog_all').form('validate');
        if (isValid)
        {           
            var lot         = $('#lot').textbox('getValue');            
            var sublot      = $('#sublot').textbox('getValue');
            var lotsublot   = lot+'dan'+sublot;
            var url         = '<?php echo site_url('report/all/cetak_all'); ?>/' + vt;
            var content     = '<iframe scrolling="auto" frameborder="0"  src="'+url+'" style="width:100%;height:100%;"></iframe>';
            var title       = vt;
            
            if ($('#tt').tabs('exists', title))
            {
                $('#tt').tabs('select', title);
                $('#dlg').dialog('close');
            } 
            else 
            {
                $('#tt').tabs('add',{
                    title:title,
                    content:content,
                    closable:true,
                    iconCls:'icon-print'
                });
                $('#dlg').dialog('close');
            }
                 
        }          
    }
</script>

<!-- End of file v_dialog_all.php -->
<!-- Location: ./views/report/all/v_dialog_all.php -->