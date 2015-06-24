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
<table id="grid-transaksi_stamping"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_stamping">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'stamping_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'stamping_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'stamping_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'stamping_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Stamping</th>
            <th data-options="field:'stamping_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_stamping = [{
        text:'New',
        id:'baruStamping',
        iconCls:'icon-new_file',
        handler:function(){transaksiStampingCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiStampingUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiStampingHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiStampingSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiStampingImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_stamping').datagrid('reload');}
    },{
        text:'Update Tgl. Stamping After',
        iconCls:'icon-date',
        handler:function(){transaksiStampingAfter();}
    },{
        text:'Update Tgl. Stamping Between',
        iconCls:'icon-date',
        handler:function(){transaksiStampingBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiStampingCheck();}
    }];
    
    $('#grid-transaksi_stamping').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/stamping/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruStamping').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiStampingCreate() {
        $('#dlg-transaksi_stamping').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_stamping').form('clear');
        //$('#stamping_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/stamping/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/stamping/getDateStamping'); ?>',function(result){
            $('#stamping_date').datebox('setValue', result.sesdate);
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
        Webcam.attach('#webcamStamping');
        
        $('#captureStamping').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureStamping').focus();
        $('#captureStamping').bind('click', function(){
            $('#captureStamping').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#stamping_lot').textbox('setValue', '');
            $('#stamping_customer').textbox('setValue', '');
            $('#stamping_barang').textbox('setValue', '');
            $('#stamping_sub').textbox('setValue', '');
            $('#stamping_lot').next().find('input').focus();
        });
       
        $('#stamping_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotStamping').focus();
            }
        });
        
        $('#chlotStamping').bind('click', function(){
            $('#stamping_sub').next().find('input').focus();
        });
        
        $('#stamping_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#stamping_oksave').focus();
            }
        });
    }
    
    function captureStamping() {
        Webcam.snap( function(data_uri) {
            $('#imgStamping').textbox('setValue',data_uri);
        });
    }
    
    function checkLotStamping() {
        var lotidStamping = $('#stamping_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/stamping/cekLot'); ?>',{stamping_lot:lotidStamping},function(result){
            if (result.success){
                $('#stamping_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/stamping/getCustItem'); ?>',{stamping_lot:lotidStamping},function(result){
                    $('#stamping_customer').textbox('setValue', result.customer);
                    $('#stamping_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#stamping_lot').textbox('setValue', '');
                $('#stamping_customer').textbox('setValue', '');
                $('#stamping_barang').textbox('setValue', '');
                $('#stamping_sub').textbox('setValue', '');
                $('#stamping_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiStampingUpdate() {
        var row = $('#grid-transaksi_stamping').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_stamping-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_stamping-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/stamping/update'); ?>/' + row.stamping_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiStampingSave(){
        $('#fm-transaksi_stamping').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_stamping').dialog('close');
                    //$('#grid-transaksi_stamping').datagrid('reload');
                    transaksiStampingCreate();
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
    
    function transaksiStampingSaveEdit(){
        $('#fm-transaksi_stamping-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_stamping-edit').dialog('close');
                    $('#grid-transaksi_stamping').datagrid('reload');
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
    
    function transaksiStampingHapus(){
        var row = $('#grid-transaksi_stamping').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Stamping '+row.stamping_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/stamping/delete'); ?>',{stamping_id:row.stamping_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_stamping').datagrid('reload');
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
    
    function transaksiStampingImage(){
        var row = $('#grid-transaksi_stamping').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/stamping/viewImage'); ?>',{stamping_id:row.stamping_id},function(result){
                if (result.success){
                    var content = '<iframe scstamping="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.stamping_id;
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

    function transaksiStampingSesdate()
    {
        $('#dlg-transaksi_stamping_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_stamping_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/stamping/getDateStamping'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiStampingSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/stamping/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_stamping_sesdate').dialog('close');
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
    
    function transaksiStampingAfter()
    {
        $('#dlg-transaksi_stamping-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Stamping After');
        $('#fm-transaksi_stamping-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/stamping/updateAfter'); ?>';
    }
    
    function transaksiStampingAfterSave()
    {
        $('#fm-transaksi_stamping-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_stamping').datagrid('reload');
                    $('#dlg-transaksi_stamping-after').dialog('close');
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
    
    function transaksiStampingBetween()
    {
        $('#dlg-transaksi_stamping-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Stamping Between');
        $('#fm-transaksi_stamping-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/stamping/updateBetween'); ?>';
    }
    
    function transaksiStampingBetweenSave()
    {
        $('#fm-transaksi_stamping-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_stamping').datagrid('reload');
                    $('#dlg-transaksi_stamping-between').dialog('close');
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
    
    function transaksiStampingCheck()
    {
        $('#dlg-transaksi_stamping-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_stamping-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/stamping/check'); ?>';
    }
    
    function transaksiStampingCheckSave()
    {
        $('#fm-transaksi_stamping-check').form('submit',{
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
    #fm-transaksi_stamping{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_stamping-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_stamping-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_stamping-between{
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

<div id="dlg-transaksi_stamping" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_stamping">
    <form id="fm-transaksi_stamping" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamStamping">
            </div>
        </div>
        <div class="center">
            <a id="captureStamping" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureStamping()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgStamping" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="stamping_lot" name="stamping_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotStamping" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotStamping()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="stamping_sub" name="stamping_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="stamping_customer" name="stamping_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="stamping_barang" name="stamping_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Stamping</label>
                <input id="stamping_date" name="stamping_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_stamping">
    <a href="javascript:void(0)" id="stamping_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiStampingSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_stamping').dialog('close');Webcam.reset();$('#grid-transaksi_stamping').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_stamping-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_stamping-edit">
    <form id="fm-transaksi_stamping-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="stamping_lot" name="stamping_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="stamping_sub" name="stamping_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Stamping</label>
            <input type="text" id="stamping_date" name="stamping_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_stamping-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiStampingSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_stamping-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_stamping_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_stamping_sesdate">
    <form id="fm-transaksi_stamping_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_stamping_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiStampingSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_stamping_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_stamping-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_stamping-after">
    <form id="fm-transaksi_stamping-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Stamping</label>
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
<div id="dlg-buttons-transaksi_stamping-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiStampingAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_stamping-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_stamping-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_stamping-between">
    <form id="fm-transaksi_stamping-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Stamping</label>
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
<div id="dlg-buttons-transaksi_stamping-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiStampingBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_stamping-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_stamping-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_stamping-check">
    <form id="fm-transaksi_stamping-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Stamping</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_stamping-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiStampingCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_stamping-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_stamping.php -->
<!-- Location: ./application/views/transaksi/v_stamping.php -->