<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>

<!-- Data Grid -->
<table id="grid-master_item"
    data-options="pageSize:50, multiSort:true, remoteSort:false, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_master_item">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'item_id'"                   width="100" align="center" sortable="true">Kode Barang</th>
            <th data-options="field:'item_name'"                 width="400" halign="center" align="left" sortable="true">Nama Barang</th>
            </tr>
    </thead>
</table>

<script type="text/javascript">
    var toolbar_master_item = [{
        text:'New',
        iconCls:'icon-new_file',
        handler:function(){masterItemCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){masterItemUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){masterItemHapus();}
    },{
        text:'Upload',
        iconCls:'icon-upload',
        handler:function(){masterItemUpload();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-master_item').datagrid('reload');}
    }];
    
    $('#grid-master_item').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('master/item/index'); ?>?grid=true'})
        .datagrid('enableFilter');
    
    function masterItemCreate() {
        $('#dlg-master_item').dialog({modal: true}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-master_item').form('clear');
        url = '<?php echo site_url('master/item/create'); ?>';
    }
    
    function masterItemUpdate() {
        var row = $('#grid-master_item').datagrid('getSelected');
        if(row){
            $('#dlg-master_item-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-master_item-edit').form('load',row);
            url = '<?php echo site_url('master/item/update'); ?>/' + row.item_id;
            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function masterItemSave(){
        $('#fm-master_item').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-master_item').dialog('close');
                    $('#grid-master_item').datagrid('reload');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Input Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Input Data Gagal'
                    });
                }
            }
        });
    }
    
    function masterItemSaveEdit(){
        $('#fm-master_item-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-master_item-edit').dialog('close');
                    $('#grid-master_item').datagrid('reload');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Ubah Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Ubah Data Gagal'
                    });
                }
            }
        });
    }
    
    function masterItemHapus(){
        var row = $('#grid-master_item').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Item '+row.item_name+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('master/item/delete'); ?>',{item_id:row.item_id},function(result){
                        if (result.success){
                            $('#grid-master_item').datagrid('reload');
                            $.messager.show({
                                title: 'Info',
                                msg: 'Hapus Data Berhasil'
                            });
                        } else {
                            $.messager.show({
                                title: 'Error',
                                msg: 'Hapus Data Gagal'
                            });
                        }
                    },'json');
                }
            });
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }

    function masterItemUpload()
    {
        $('#dlg-master_item-upload').dialog({modal: true}).dialog('open').dialog('setTitle','Upload File');
        $('#fm-master_item-upload').form('reset');
        urls = '<?php echo site_url('master/item/upload'); ?>/';
    }
    
    function masterItemUploadSave()
    {
        $('#fm-master_item-upload').form('submit',{
            url: urls,
            onSubmit: function(){   
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success)
                {
                    
                    $('#dlg-master_item-upload').dialog('close');
                    $('#grid-master_item').datagrid('reload');
                    $.messager.show({
                            title: 'Info',
                            msg: result.total + ' ' +result.ok + ' ' + result.ng
                            });
                } 
                else 
                {
                    $.messager.show({
                    title: 'Error',
                    msg: 'Upload Data Gagal'
                });
                }
            }
        });
    }
    
</script>

<style type="text/css">
    #fm-master_item{
        margin:0;
        padding:10px 30px;
    }
     #fm-master_item-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-master_item-upload{
        margin:0;
        padding:10px 30px;
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
    .fitem input{
        display:inline-block;
        width:150px;
    }
</style>

<div id="dlg-master_item-upload" class="easyui-dialog" style="width:400px; height:150px; padding: 10px 20px" closed="true" buttons="#dlg_buttons-master_item-upload">
    <form id="fm-master_item-upload" method="post" enctype="multipart/form-data" novalidate>       
        <div class="fitem">
            <label for="type">File</label>
            <input id="filea" name="filea" class="easyui-filebox" required="true"/>
        </div> 
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg_buttons-master_item-upload">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="masterItemUploadSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-master_item-upload').dialog('close')">Batal</a>
</div>
<!-- ----------- -->
<div id="dlg-master_item" class="easyui-dialog" style="width:600px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-master_item">
    <form id="fm-master_item" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Kode Barang</label>
            <input type="text" id="item_id" name="item_id" class="easyui-numberbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Nama Barang</label>
            <input type="text" id="item_name" name="item_name" style="width:350px;"  class="easyui-textbox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-master_item">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="masterItemSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-master_item').dialog('close')">Batal</a>
</div>

<div id="dlg-master_item-edit" class="easyui-dialog" style="width:600px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-master_item-edit">
    <form id="fm-master_item-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Nama Barang</label>
            <input type="text" id="item_name" name="item_name" style="width:350px;" class="easyui-textbox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-master_item-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="masterItemSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-master_item-edit').dialog('close')">Batal</a>
</div>
<!-- End of file v_item.php -->
<!-- Location: ./application/views/master/v_item.php -->