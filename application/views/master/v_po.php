<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>

<!-- Data Grid -->
<table id="grid-master_po"
    data-options="pageSize:50, multiSort:true, remoteSort:false, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_master_po">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'po_no'"                 width="100" halign="center" align="left" sortable="true">No PO</th>
            <th data-options="field:'po_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal PO</th>
            <th data-options="field:'lot_no'"                   width="100" align="center" sortable="true">No Lot</th>
            <th data-options="field:'cust_name'"                 width="250" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="250" halign="center" align="left" sortable="true">Nama Barang</th>           
            <th data-options="field:'po_qty'"                 width="100" halign="center" align="right" sortable="true" formatter="thousandSep">Qty PO</th>
            <th data-options="field:'po_prod'"                 width="100" halign="center" align="right" sortable="true" formatter="thousandSep">Prod PO</th>
        </tr>
    </thead>
</table>

<script type="text/javascript">
    var toolbar_master_po = [{
        text:'New',
        iconCls:'icon-new_file',
        handler:function(){masterPoCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){masterPoUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){masterPoHapus();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-master_po').datagrid('reload');}
    }];
    
    $('#grid-master_po').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('master/po/index'); ?>?grid=true'})
        .datagrid('enableFilter');
    
    function masterPoCreate() {
        $('#dlg-master_po').dialog({modal: true}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-master_po').form('clear');
        url = '<?php echo site_url('master/po/create'); ?>';
    }
    
    function masterPoUpdate() {
        var row = $('#grid-master_po').datagrid('getSelected');
        if(row){
            $('#dlg-master_po-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-master_po-edit').form('load',row);
            url = '<?php echo site_url('master/po/update'); ?>/' + row.lot_no;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function masterPoSave(){
        $('#fm-master_po').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-master_po').dialog('close');
                    $('#grid-master_po').datagrid('reload');
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
    
    function masterPoSaveEdit(){
        $('#fm-master_po-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-master_po-edit').dialog('close');
                    $('#grid-master_po').datagrid('reload');
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
    
    function masterPoHapus(){
        var row = $('#grid-master_po').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Lot '+row.lot_no+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('master/po/delete'); ?>',{lot_no:row.lot_no},function(result){
                        if (result.success){
                            $('#grid-master_po').datagrid('reload');
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
    
    function thousandSep(value,row,index)
    {
        if (value == 0)
        {
            return "";
        }
        else
        {
            return accounting.formatMoney(value, "", 0, ".", ",");
        }        
    }
    
    function dateboxFormatter(date)
    {
        var y = date.getFullYear();
        var m = date.getMonth()+1;
        var d = date.getDate();
        return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
    }
    function dateboxParser(s)
    {
        if (!s) return new Date();
        var ss = (s.split('-'));
        var y = parseInt(ss[0],10);
        var m = parseInt(ss[1],10);
        var d = parseInt(ss[2],10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
            return new Date(y,m-1,d);
        } else {
            return new Date();
        }
    }
    
</script>

<style type="text/css">
    #fm-master_po{
        margin:0;
        padding:10px 30px;
    }
     #fm-master_po-edit{
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

<div id="dlg-master_po" class="easyui-dialog" style="width:600px; height:310px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-master_po">
    <form id="fm-master_po" method="post" novalidate> 
        <div class="fitem">
            <label for="type">No PO</label>
            <input type="text" id="po_no" name="po_no" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal PO</label>
            <input type="text" id="po_date" name="po_date" class="easyui-datebox" data-options="
                formatter:dateboxFormatter, parser:dateboxParser" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="lot_no" name="lot_no" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Nama Customer</label>
            <input type="text" id="po_cust" name="po_cust" style="width: 350px;" class="easyui-combobox" data-options="
                url:'<?php echo site_url('master/po/getCust'); ?>',
                method:'get', valueField:'cust_id', textField:'cust_name', panelHeight:'300'" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Nama Barang</label>
            <input type="text" id="po_item" name="po_item" style="width: 350px;" class="easyui-combobox" data-options="
                url:'<?php echo site_url('master/po/getItem'); ?>',
                method:'get', valueField:'item_id', textField:'item_name', panelHeight:'300'" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Qty PO</label>
            <input type="text" id="po_qty" name="po_qty" class="easyui-numberbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Qty Prod</label>
            <input type="text" id="po_prod" name="po_prod" class="easyui-numberbox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-master_po">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="masterPoSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-master_po').dialog('close')">Batal</a>
</div>

<div id="dlg-master_po-edit" class="easyui-dialog" style="width:600px; height:310px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-master_po-edit">
    <form id="fm-master_po-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No PO</label>
            <input type="text" id="po_no" name="po_no" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal PO</label>
            <input type="text" id="po_date" name="po_date" class="easyui-datebox" data-options="
                formatter:dateboxFormatter, parser:dateboxParser" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Nama Customer</label>
            <input type="text" id="po_cust" name="po_cust" style="width: 350px;" class="easyui-combobox" data-options="
                url:'<?php echo site_url('master/po/getCust'); ?>',
                method:'get', valueField:'cust_id', textField:'cust_name', panelHeight:'300'" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Nama Barang</label>
            <input type="text" id="po_item" name="po_item" style="width: 350px;" class="easyui-combobox" data-options="
                url:'<?php echo site_url('master/po/getItem'); ?>',
                method:'get', valueField:'item_id', textField:'item_name', panelHeight:'300'" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Qty PO</label>
            <input type="text" id="po_qty" name="po_qty" class="easyui-numberbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Qty Prod</label>
            <input type="text" id="po_prod" name="po_prod" class="easyui-numberbox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-master_po-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="masterPoSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-master_po-edit').dialog('close')">Batal</a>
</div>
<!-- End of file v_po.php -->
<!-- Location: ./application/views/master/v_po.php -->