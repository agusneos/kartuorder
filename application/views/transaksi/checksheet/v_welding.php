<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/webcamjs/webcam.js')?>"></script>
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
<table id="grid-transaksi_welding"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_welding">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'welding_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'welding_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'welding_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'welding_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Welding</th>
            <th data-options="field:'welding_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_welding = [{
        text:'New',
        id:'baruWelding',
        iconCls:'icon-new_file',
        handler:function(){transaksiWeldingCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiWeldingUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiWeldingHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiWeldingSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiWeldingImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_welding').datagrid('reload');}
    },{
        text:'Update Tgl. Welding After',
        iconCls:'icon-date',
        handler:function(){transaksiWeldingAfter();}
    },{
        text:'Update Tgl. Welding Between',
        iconCls:'icon-date',
        handler:function(){transaksiWeldingBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiWeldingCheck();}
    }];
    
    $('#grid-transaksi_welding').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/welding/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruWelding').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiWeldingCreate() {
        $('#dlg-transaksi_welding').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_welding').form('clear');
        //$('#welding_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/welding/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/welding/getDateWelding'); ?>',function(result){
            $('#welding_date').datebox('setValue', result.sesdate);
            },'json'); //ambil session date

        Webcam.set({
            width: 430,
            height: 240,
            dest_width: 1280,
            dest_height: 720,
            force_flash: true,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
        Webcam.setSWFLocation('assets/webcamjs/webcam.swf');
        Webcam.attach('#webcamWelding');
        
        $('#captureWelding').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureWelding').focus();
        $('#captureWelding').bind('click', function(){
            $('#captureWelding').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#welding_lot').textbox('setValue', '');
            $('#welding_customer').textbox('setValue', '');
            $('#welding_barang').textbox('setValue', '');
            $('#welding_sub').textbox('setValue', '');
            $('#welding_lot').next().find('input').focus();
        });
       
        $('#welding_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotWelding').focus();
            }
        });
        
        $('#chlotWelding').bind('click', function(){
            $('#welding_sub').next().find('input').focus();
        });
        
        $('#welding_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#welding_oksave').focus();
            }
        });
    }
    
    function captureWelding() {
        Webcam.snap( function(data_uri) {
            $('#imgWelding').textbox('setValue',data_uri);
        });
    }
    
    function checkLotWelding() {
        var lotidWelding = $('#welding_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/welding/cekLot'); ?>',{welding_lot:lotidWelding},function(result){
            if (result.success){
                $('#welding_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/welding/getCustItem'); ?>',{welding_lot:lotidWelding},function(result){
                    $('#welding_customer').textbox('setValue', result.customer);
                    $('#welding_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#welding_lot').textbox('setValue', '');
                $('#welding_customer').textbox('setValue', '');
                $('#welding_barang').textbox('setValue', '');
                $('#welding_sub').textbox('setValue', '');
                $('#welding_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiWeldingUpdate() {
        var row = $('#grid-transaksi_welding').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_welding-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_welding-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/welding/update'); ?>/' + row.welding_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiWeldingSave(){
        $('#fm-transaksi_welding').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_welding').dialog('close');
                    //$('#grid-transaksi_welding').datagrid('reload');
                    transaksiWeldingCreate();
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
    
    function transaksiWeldingSaveEdit(){
        $('#fm-transaksi_welding-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_welding-edit').dialog('close');
                    $('#grid-transaksi_welding').datagrid('reload');
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
    
    function transaksiWeldingHapus(){
        var row = $('#grid-transaksi_welding').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Welding '+row.welding_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/welding/delete'); ?>',{welding_id:row.welding_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_welding').datagrid('reload');
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
    
    function transaksiWeldingImage(){
        var row = $('#grid-transaksi_welding').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/welding/viewImage'); ?>',{welding_id:row.welding_id},function(result){
                if (result.success){
                    var content = '<iframe scwelding="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.welding_id;
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

    function transaksiWeldingSesdate()
    {
        $('#dlg-transaksi_welding_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_welding_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/welding/getDateWelding'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiWeldingSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/welding/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_welding_sesdate').dialog('close');
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
    
    function transaksiWeldingAfter()
    {
        $('#dlg-transaksi_welding-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Welding After');
        $('#fm-transaksi_welding-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/welding/updateAfter'); ?>';
    }
    
    function transaksiWeldingAfterSave()
    {
        $('#fm-transaksi_welding-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_welding').datagrid('reload');
                    $('#dlg-transaksi_welding-after').dialog('close');
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
    
    function transaksiWeldingBetween()
    {
        $('#dlg-transaksi_welding-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Welding Between');
        $('#fm-transaksi_welding-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/welding/updateBetween'); ?>';
    }
    
    function transaksiWeldingBetweenSave()
    {
        $('#fm-transaksi_welding-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_welding').datagrid('reload');
                    $('#dlg-transaksi_welding-between').dialog('close');
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
    
    function transaksiWeldingCheck()
    {
        $('#dlg-transaksi_welding-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_welding-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/welding/check'); ?>';
    }
    
    function transaksiWeldingCheckSave()
    {
        $('#fm-transaksi_welding-check').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success)
                {
                    $.messager.show({
                        title: 'Info',
                        msg: 'Total Entry Data Tanggal '+result.tgl+' Sebanyak '+result.total+' Data'
                    }); 
                }                               
            }
        });
    }
    
</script>
<style type="text/css">
    #fm-transaksi_welding{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_welding-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_welding-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_welding-between{
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

<div id="dlg-transaksi_welding" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_welding">
    <form id="fm-transaksi_welding" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamWelding">
            </div>
        </div>
        <div class="center">
            <a id="captureWelding" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureWelding()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgWelding" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="welding_lot" name="welding_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotWelding" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotWelding()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="welding_sub" name="welding_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="welding_customer" name="welding_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="welding_barang" name="welding_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Welding</label>
                <input id="welding_date" name="welding_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_welding">
    <a href="javascript:void(0)" id="welding_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiWeldingSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_welding').dialog('close');Webcam.reset();$('#grid-transaksi_welding').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_welding-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_welding-edit">
    <form id="fm-transaksi_welding-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="welding_lot" name="welding_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="welding_sub" name="welding_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Welding</label>
            <input type="text" id="welding_date" name="welding_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_welding-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiWeldingSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_welding-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_welding_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_welding_sesdate">
    <form id="fm-transaksi_welding_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_welding_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiWeldingSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_welding_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_welding-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_welding-after">
    <form id="fm-transaksi_welding-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Welding</label>
            <input type="text" id="aa" name="aa" class="easyui-datebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Setelah Input</label>
            <input type="text" id="bb" name="bb" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Update Ke Tanggal</label>
            <input type="text" id="cc" name="cc" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_welding-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiWeldingAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_welding-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_welding-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_welding-between">
    <form id="fm-transaksi_welding-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Welding</label>
            <input type="text" id="dd" name="dd" class="easyui-datebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Setelah Input</label>
            <input type="text" id="ee" name="ee" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sebelum Input</label>
            <input type="text" id="ff" name="ff" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Update Ke Tanggal</label>
            <input type="text" id="gg" name="gg" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_welding-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiWeldingBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_welding-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_welding-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_welding-check">
    <form id="fm-transaksi_welding-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Welding</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_welding-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiWeldingCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_welding-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_welding.php -->
<!-- Location: ./application/views/transaksi/v_welding.php -->