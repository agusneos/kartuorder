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
<table id="grid-transaksi_rolling"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_rolling">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'rolling_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'rolling_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'rolling_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'rolling_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Rolling</th>
            <th data-options="field:'rolling_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_rolling = [{
        text:'New',
        id:'baruRolling',
        iconCls:'icon-new_file',
        handler:function(){transaksiRollingCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiRollingUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiRollingHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiRollingSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiRollingImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_rolling').datagrid('reload');}
    },{
        text:'Update Tgl. Rolling After',
        iconCls:'icon-date',
        handler:function(){transaksiRollingAfter();}
    },{
        text:'Update Tgl. Rolling Between',
        iconCls:'icon-date',
        handler:function(){transaksiRollingBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiRollingCheck();}
    }];
    
    $('#grid-transaksi_rolling').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/rolling/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruRolling').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiRollingCreate() {
        $('#dlg-transaksi_rolling').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_rolling').form('clear');
        //$('#rolling_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/rolling/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/rolling/getDateRolling'); ?>',function(result){
            $('#rolling_date').datebox('setValue', result.sesdate);
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
        Webcam.attach('#webcamRolling');
        
        $('#captureRolling').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureRolling').focus();
        $('#captureRolling').bind('click', function(){
            $('#captureRolling').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#rolling_lot').textbox('setValue', '');
            $('#rolling_customer').textbox('setValue', '');
            $('#rolling_barang').textbox('setValue', '');
            $('#rolling_sub').textbox('setValue', '');
            $('#rolling_lot').next().find('input').focus();
        });
       
        $('#rolling_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotRolling').focus();
            }
        });
        
        $('#chlotRolling').bind('click', function(){
            $('#rolling_sub').next().find('input').focus();
        });
        
        $('#rolling_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#rolling_oksave').focus();
            }
        });
    }
    
    function captureRolling() {
        Webcam.snap( function(data_uri) {
            $('#imgRolling').textbox('setValue',data_uri);
        });
    }
    
    function checkLotRolling() {
        var lotidRolling = $('#rolling_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/rolling/cekLot'); ?>',{rolling_lot:lotidRolling},function(result){
            if (result.success){
                $('#rolling_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/rolling/getCustItem'); ?>',{rolling_lot:lotidRolling},function(result){
                    $('#rolling_customer').textbox('setValue', result.customer);
                    $('#rolling_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#rolling_lot').textbox('setValue', '');
                $('#rolling_customer').textbox('setValue', '');
                $('#rolling_barang').textbox('setValue', '');
                $('#rolling_sub').textbox('setValue', '');
                $('#rolling_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiRollingUpdate() {
        var row = $('#grid-transaksi_rolling').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_rolling-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_rolling-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/rolling/update'); ?>/' + row.rolling_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiRollingSave(){
        $('#fm-transaksi_rolling').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_rolling').dialog('close');
                    //$('#grid-transaksi_rolling').datagrid('reload');
                    transaksiRollingCreate();
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
    
    function transaksiRollingSaveEdit(){
        $('#fm-transaksi_rolling-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_rolling-edit').dialog('close');
                    $('#grid-transaksi_rolling').datagrid('reload');
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
    
    function transaksiRollingHapus(){
        var row = $('#grid-transaksi_rolling').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Rolling '+row.rolling_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/rolling/delete'); ?>',{rolling_id:row.rolling_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_rolling').datagrid('reload');
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
    
    function transaksiRollingImage(){
        var row = $('#grid-transaksi_rolling').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/rolling/viewImage'); ?>',{rolling_id:row.rolling_id},function(result){
                if (result.success){
                    var content = '<iframe scrolling="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.rolling_id;
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

    function transaksiRollingSesdate()
    {
        $('#dlg-transaksi_rolling_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_rolling_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/rolling/getDateRolling'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiRollingSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/rolling/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_rolling_sesdate').dialog('close');
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
    
    function transaksiRollingAfter()
    {
        $('#dlg-transaksi_rolling-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Rolling After');
        $('#fm-transaksi_rolling-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/rolling/updateAfter'); ?>';
    }
    
    function transaksiRollingAfterSave()
    {
        $('#fm-transaksi_rolling-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_rolling').datagrid('reload');
                    $('#dlg-transaksi_rolling-after').dialog('close');
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
    
    function transaksiRollingBetween()
    {
        $('#dlg-transaksi_rolling-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Rolling Between');
        $('#fm-transaksi_rolling-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/rolling/updateBetween'); ?>';
    }
    
    function transaksiRollingBetweenSave()
    {
        $('#fm-transaksi_rolling-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_rolling').datagrid('reload');
                    $('#dlg-transaksi_rolling-between').dialog('close');
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
    
    function transaksiRollingCheck()
    {
        $('#dlg-transaksi_rolling-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_rolling-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/rolling/check'); ?>';
    }
    
    function transaksiRollingCheckSave()
    {
        $('#fm-transaksi_rolling-check').form('submit',{
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
    #fm-transaksi_rolling{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_rolling-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_rolling-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_rolling-between{
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

<div id="dlg-transaksi_rolling" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_rolling">
    <form id="fm-transaksi_rolling" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamRolling">
            </div>
        </div>
        <div class="center">
            <a id="captureRolling" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureRolling()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgRolling" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="rolling_lot" name="rolling_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotRolling" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotRolling()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="rolling_sub" name="rolling_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="rolling_customer" name="rolling_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="rolling_barang" name="rolling_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Rolling</label>
                <input id="rolling_date" name="rolling_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_rolling">
    <a href="javascript:void(0)" id="rolling_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiRollingSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_rolling').dialog('close');Webcam.reset();$('#grid-transaksi_rolling').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_rolling-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_rolling-edit">
    <form id="fm-transaksi_rolling-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="rolling_lot" name="rolling_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="rolling_sub" name="rolling_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Rolling</label>
            <input type="text" id="rolling_date" name="rolling_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_rolling-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiRollingSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_rolling-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_rolling_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_rolling_sesdate">
    <form id="fm-transaksi_rolling_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_rolling_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiRollingSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_rolling_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_rolling-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_rolling-after">
    <form id="fm-transaksi_rolling-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Rolling</label>
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
<div id="dlg-buttons-transaksi_rolling-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiRollingAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_rolling-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_rolling-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_rolling-between">
    <form id="fm-transaksi_rolling-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Rolling</label>
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
<div id="dlg-buttons-transaksi_rolling-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiRollingBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_rolling-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_rolling-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_rolling-check">
    <form id="fm-transaksi_rolling-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Rolling</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_rolling-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiRollingCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_rolling-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_rolling.php -->
<!-- Location: ./application/views/transaksi/v_rolling.php -->