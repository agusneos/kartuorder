<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/webcamjs/webcam.js')?>"></script>

<!-- Data Grid -->
<table id="grid-transaksi_ordercard"
    data-options="pageSize:50, multiSort:true, remoteSort:false, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_ordercard">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'ordcard_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'ordcard_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'ordcard_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'ordcard_packing'"                 width="100" halign="center" align="center" sortable="true">Tanggal Packing</th>
            <th data-options="field:'ordcard_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript">    
    var toolbar_transaksi_ordercard = [{
        text:'New',
        iconCls:'icon-new_file',
        handler:function(){transaksiOrdercardCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiOrdercardUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiOrdercardHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiOrdercardSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiOrdercardImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_ordercard').datagrid('reload');}
    }];
    
    $('#grid-transaksi_ordercard').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/ordercard/index'); ?>?grid=true'})
        .datagrid('enableFilter');
    
    function transaksiOrdercardCreate() {
        $('#dlg-transaksi_ordercard').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_ordercard').form('clear');
        url = '<?php echo site_url('transaksi/ordercard/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/ordercard/getDatePacking'); ?>',function(result){
            $('#ordcard_packing').datebox('setValue', result.sesdate);
            },'json'); //ambil session date

        Webcam.set({
            width: 427,
            height: 240,
            dest_width: 1280,
            dest_height: 720,
            image_format: 'jpeg',
            jpeg_quality: 99
        });
        Webcam.setSWFLocation('assets/webcamjs/webcam.swf');
        Webcam.attach( '#webcam' );
        
        $('#capture').focus();
        $('#capture').bind('click', function(){
            $('#ordcard_lot').next().find('input').focus();
        });
        $('#ordcard_lot').textbox('textbox').keypress(function(e){
            if (e.keyCode == 13){                
                $('#ordcard_sub').next().find('input').focus();
            }
        });
        $('#ordcard_sub').textbox('textbox').keyup(function(e){
            if (e.keyCode == 13){
                
            var lotid = $('#ordcard_lot').textbox('getValue');
                $.post('<?php echo site_url('transaksi/ordercard/cekLot'); ?>',{ordcard_lot:lotid},function(result){
                        if (result.success){
                            $('#ordcard_sub').next().find('input').focus();
                            $.post('<?php echo site_url('transaksi/ordercard/getCustItem'); ?>',{ordcard_lot:lotid},function(result){
                                $('#customer').textbox('setValue', result.customer);
                                $('#barang').textbox('setValue', result.barang);
                            },'json');
                        } else {
                            $('#ordcard_lot').textbox('setValue', '');
                            $('#ordcard_lot').next().find('input').focus();
                            $.messager.show({
                                title: 'Error',
                                msg: 'Lot Tidak Ditemukan'
                            });
                        }
                    },'json');
            }
        });
        $('#ordcard_sub').textbox('textbox').keydown(function(e){
            if (e.keyCode == 13){
                    $('#oksave').focus();
            }
        });
    }
    
    function transaksiOrdercardUpdate() {
        var row = $('#grid-transaksi_ordercard').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_ordercard-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_ordercard-edit').form('load',row);
            url = '<?php echo site_url('transaksi/ordercard/update'); ?>/' + row.ordcard_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiOrdercardSave(){
        $('#fm-transaksi_ordercard').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_ordercard').dialog('close');
                    $('#grid-transaksi_ordercard').datagrid('reload');
                    transaksiOrdercardCreate();
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
    
    function transaksiOrdercardSaveEdit(){
        $('#fm-transaksi_ordercard-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_ordercard-edit').dialog('close');
                    $('#grid-transaksi_ordercard').datagrid('reload');
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
    
    function transaksiOrdercardHapus(){
        var row = $('#grid-transaksi_ordercard').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Ordercard '+row.ordcard_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/ordercard/delete'); ?>',{ordcard_id:row.ordcard_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_ordercard').datagrid('reload');
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
    
    function transaksiOrdercardImage(){
        var row = $('#grid-transaksi_ordercard').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/ordercard/viewImage'); ?>',{ordcard_id:row.ordcard_id},function(result){
                if (result.success){
                    var content = '<iframe scrolling="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.ordcard_id;
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
                            iconCls:'icon-picture'
                        });
                        $('#dlg').dialog('close');
                    }
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Gambar Tidak Ditemukan'
                    });
                }
            },'json');
            
        }
        else
        {
            $.messager.alert('Info','Data belum dipilih !','info');
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
    
    function capture() {
        Webcam.snap( function(data_uri) {
            $('#img').textbox('setValue',data_uri);
        });
    }
    
    function transaksiOrdercardSesdate()
    {
        $('#dlg-transaksi_ordercard_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_ordercard_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/ordercard/getDatePacking'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiOrdercardSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/ordercard/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_ordercard_sesdate').dialog('close');
                $.messager.show({
                    title: 'Info',
                    msg: 'Ubah Data Berhasil'
                });
            }
            else
            {
                $.messager.show({
                    title: 'Error',
                    msg: 'Ubah Data Gagal'
                });
            }
        },'json');
    }

</script>
<style type="text/css">
    #fm-transaksi_ordercard{
        margin:0;
        padding:10px 30px;
    }
     #fm-transaksi_ordercard-edit{
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
    .center a{
        margin-left: 200px;
        margin-top: 5px;
        margin-bottom: 10px;
    }
    .grup2{
        margin-left: 75px;
    }

</style>

<div id="dlg-transaksi_ordercard" class="easyui-dialog" style="width:600px; height:570px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_ordercard">
    <form id="fm-transaksi_ordercard" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcam">
            </div>
        </div>
        <div class="center">
            <a id="capture" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="capture()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type">Data</label>
                <input id="img" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="ordcard_lot" name="ordcard_lot" class="easyui-textbox" required="true"/>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="ordcard_sub" name="ordcard_sub" class="easyui-textbox" required="true"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="customer" name="customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="barang" name="barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Packing</label>
                <input id="ordcard_packing" name="ordcard_packing" class="easyui-datebox" data-options="
                    formatter:dateboxFormatter, parser:dateboxParser" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_ordercard">
    <a href="javascript:void(0)" id="oksave" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiOrdercardSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_ordercard').dialog('close');Webcam.reset();">Batal</a>
</div>

<div id="dlg-transaksi_ordercard-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_ordercard-edit">
    <form id="fm-transaksi_ordercard-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="ordcard_lot" name="ordcard_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="ordcard_sub" name="ordcard_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Packing</label>
            <input type="text" id="ordcard_packing" name="ordcard_packing" class="easyui-datebox" data-options="
                formatter:dateboxFormatter, parser:dateboxParser" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_ordercard-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiOrdercardSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_ordercard-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_ordercard_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_ordercard_sesdate">
    <form id="fm-transaksi_ordercard_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" data-options="
                formatter:dateboxFormatter, parser:dateboxParser" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_ordercard_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiOrdercardSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_ordercard_sesdate').dialog('close')">Batal</a>
</div>
<!-- End of file v_ordercard.php -->
<!-- Location: ./application/views/transaksi/v_ordercard.php -->