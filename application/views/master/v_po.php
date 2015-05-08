<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>
<script type="text/javascript">
    $.extend($.fn.datebox.defaults,{
        formatter:function(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        },
        parser:function(s){
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
    });
        
    $.extend($.fn.datetimebox.defaults,{
        formatter:function(date){
            var h = date.getHours();
            var M = date.getMinutes();
            var s = date.getSeconds();
            function formatNumber(value){
                return (value < 10 ? '0' : '') + value;
            }
            var separator = $(this).datetimebox('spinner').timespinner('options').separator;
            var r = $.fn.datebox.defaults.formatter(date) + ' ' + formatNumber(h)+separator+formatNumber(M);
            if ($(this).datetimebox('options').showSeconds){
                r += separator+formatNumber(s);
            }
            return r;
        },
        parser:function(s){
            if ($.trim(s) == ''){
                return new Date();
            }
            var dt = s.split(' ');
            var d = $.fn.datebox.defaults.parser(dt[0]);
            if (dt.length < 2){
                return d;
            }
            var separator = $(this).datetimebox('spinner').timespinner('options').separator;
            var tt = dt[1].split(separator);
            var hour = parseInt(tt[0], 10) || 0;
            var minute = parseInt(tt[1], 10) || 0;
            var second = parseInt(tt[2], 10) || 0;
            return new Date(d.getFullYear(), d.getMonth(), d.getDate(), hour, minute, second);
        }
    });
</script>
<!-- Data Grid -->
<table id="grid-master_po"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_master_po">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'po_no'"            width="100" halign="center" align="left"    sortable="true" >No PO</th>
            <th data-options="field:'po_date'"          width="100" halign="center" align="center"  sortable="true" >Tanggal PO</th>
            <th data-options="field:'lot_no'"           width="100" halign="center" align="center"  sortable="true" >No Lot</th>
            <th data-options="field:'c.cust_name'"      width="250" halign="center" align="left"    sortable="true" >Nama Customer</th>
            <th data-options="field:'b.item_name'"      width="250" halign="center" align="left"    sortable="true" >Nama Barang</th>           
            <th data-options="field:'po_qty'"           width="100" halign="center" align="right"   sortable="true" formatter="thousandSep" >Qty PO</th>
            <th data-options="field:'po_prod'"          width="100" halign="center" align="right"   sortable="true" formatter="thousandSep" >Prod PO</th>
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
        text:'Upload',
        iconCls:'icon-upload',
        handler:function(){masterPoUpload();}
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
        $('#lot_no').textbox('enable');
    }
    
    function masterPoUpdate() {
        var row = $('#grid-master_po').datagrid('getSelected');
        if(row){
            $('#dlg-master_po').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-master_po').form('load',row);
            url = '<?php echo site_url('master/po/update'); ?>/' + row.lot_no;
            $('#lot_no').textbox('disable');
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
                        msg: 'Input Data Gagal, LOT Sudah Ada'
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
        
    function masterPoUpload()
    {
        $('#dlg-master_po-upload').dialog({modal: true}).dialog('open').dialog('setTitle','Upload File');
        $('#fm-master_po-upload').form('reset');
        urls = '<?php echo site_url('master/po/upload'); ?>/';
    }
    
    function masterPoUploadSave()
    {
        $('#fm-master_po-upload').form('submit',{
            url: urls,
            onSubmit: function(){   
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success)
                {
                    
                    $('#dlg-master_po-upload').dialog('close');
                    $('#grid-master_po').datagrid('reload');
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
    #fm-master_po{
        margin:0;
        padding:10px 30px;
    }
     #fm-master_po-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-master_po-upload{
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

<div id="dlg-master_po-upload" class="easyui-dialog" style="width:400px; height:150px; padding: 10px 20px" closed="true" buttons="#dlg_buttons-master_po-upload">
    <form id="fm-master_po-upload" method="post" enctype="multipart/form-data" novalidate>       
        <div class="fitem">
            <label for="type">File</label>
            <input id="fileu" name="fileu" class="easyui-filebox" required="true"/>
        </div> 
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg_buttons-master_po-upload">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="masterPoUploadSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-master_po-upload').dialog('close')">Batal</a>
</div>
<!-- ----------- -->


<div id="dlg-master_po" class="easyui-dialog" style="width:600px; height:310px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-master_po">
    <form id="fm-master_po" method="post" novalidate> 
        <div class="fitem">
            <label for="type">No PO</label>
            <input type="text" id="po_no" name="po_no" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal PO</label>
            <input type="text" id="po_date" name="po_date" class="easyui-datebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="lot_no" name="lot_no" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Nama Customer</label>
            <input type="text" id="po_cust" name="po_cust" style="width: 350px;" class="easyui-combogrid" required="true"
                data-options="
                    panelWidth: 500,
                    idField: 'cust_id',
                    textField: 'cust_name',
                    url:'<?php echo site_url('master/po/getCust'); ?>',
                    mode:'remote',
                    fitColumns: true,
                    columns: [[
                        {field:'cust_id',title:'Kode',width:50,align:'center'},
                        {field:'cust_name',title:'Nama',width:120,halign:'center'}
                    ]]
                "
            />
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
<!-- End of file v_po.php -->
<!-- Location: ./application/views/master/v_po.php -->