<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>

<!-- Data Grid -->
<table id="grid-master_customer"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_master_customer">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'cust_id'"                   width="100" align="center" sortable="true">Kode Pelanggan</th>
            <th data-options="field:'cust_name'"                 width="400" halign="center" align="left" sortable="true">Nama Pelanggan</th>
            </tr>
    </thead>
</table>

<script type="text/javascript">
    var toolbar_master_customer = [{
        text:'New',
        iconCls:'icon-new_file',
        handler:function(){masterCustomerCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){masterCustomerUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){masterCustomerHapus();}
    },{
        text:'Upload',
        iconCls:'icon-upload',
        handler:function(){masterCustomerUpload();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-master_customer').datagrid('reload');}
    }];
    
    $('#grid-master_customer').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('master/customer/index'); ?>?grid=true'})
        .datagrid('enableFilter');
    
    function masterCustomerCreate() {
        $('#dlg-master_customer').dialog({modal: true}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-master_customer').form('clear');
        url = '<?php echo site_url('master/customer/create'); ?>';
        $('#cust_id').textbox('enable');
    }
    
    function masterCustomerUpdate() {
        var row = $('#grid-master_customer').datagrid('getSelected');
        if(row){
            $('#dlg-master_customer').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-master_customer').form('load',row);
            url = '<?php echo site_url('master/customer/update'); ?>/' + row.cust_id;
            $('#cust_id').textbox('disable');
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function masterCustomerSave(){
        $('#fm-master_customer').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-master_customer').dialog('close');
                    $('#grid-master_customer').datagrid('reload');
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
        
    function masterCustomerHapus(){
        var row = $('#grid-master_customer').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Customer '+row.cust_name+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('master/customer/delete'); ?>',{cust_id:row.cust_id},function(result){
                        if (result.success){
                            $('#grid-master_customer').datagrid('reload');
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

    function masterCustomerUpload()
    {
        $('#dlg-master_customer-upload').dialog({modal: true}).dialog('open').dialog('setTitle','Upload File');
        $('#fm-master_customer-upload').form('reset');
        urls = '<?php echo site_url('master/customer/upload'); ?>/';
    }
    
    function masterCustomerUploadSave()
    {
        $('#fm-master_customer-upload').form('submit',{
            url: urls,
            onSubmit: function(){   
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success)
                {
                    
                    $('#dlg-master_customer-upload').dialog('close');
                    $('#grid-master_customer').datagrid('reload');
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
    #fm-master_customer{
        margin:0;
        padding:10px 30px;
    }
    #fm-master_customer-upload{
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

<div id="dlg-master_customer-upload" class="easyui-dialog" style="width:400px; height:150px; padding: 10px 20px" closed="true" buttons="#dlg_buttons-master_customer-upload">
    <form id="fm-master_customer-upload" method="post" enctype="multipart/form-data" novalidate>       
        <div class="fitem">
            <label for="type">File</label>
            <input id="fileb" name="fileb" class="easyui-filebox" required="true"/>
        </div> 
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg_buttons-master_customer-upload">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="masterCustomerUploadSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-master_customer-upload').dialog('close')">Batal</a>
</div>
<!-- ----------- -->
<div id="dlg-master_customer" class="easyui-dialog" style="width:600px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-master_customer">
    <form id="fm-master_customer" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Kode Pelanggan</label>
            <input type="text" id="cust_id" name="cust_id" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Nama Pelanggan</label>
            <input type="text" id="cust_name" name="cust_name" style="width:350px;" class="easyui-textbox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-master_customer">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="masterCustomerSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-master_customer').dialog('close')">Batal</a>
</div>

<!-- End of file v_customer.php -->
<!-- Location: ./application/views/master/v_customer.php -->