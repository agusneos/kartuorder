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
<table id="grid-transaksi_washing"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_washing">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'washing_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'washing_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'washing_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'washing_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Washing</th>
            <th data-options="field:'washing_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_washing = [{
        text:'New',
        id:'baruWashing',
        iconCls:'icon-new_file',
        handler:function(){transaksiWashingCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiWashingUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiWashingHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiWashingSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiWashingImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_washing').datagrid('reload');}
    },{
        text:'Update Tgl. Washing After',
        iconCls:'icon-date',
        handler:function(){transaksiWashingAfter();}
    },{
        text:'Update Tgl. Washing Between',
        iconCls:'icon-date',
        handler:function(){transaksiWashingBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiWashingCheck();}
    }];
    
    $('#grid-transaksi_washing').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/washing/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruWashing').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiWashingCreate() {
        $('#dlg-transaksi_washing').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_washing').form('clear');
        //$('#washing_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/washing/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/washing/getDateWashing'); ?>',function(result){
            $('#washing_date').datebox('setValue', result.sesdate);
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
        Webcam.attach('#webcamWashing');
        
        $('#captureWashing').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureWashing').focus();
        $('#captureWashing').bind('click', function(){
            $('#captureWashing').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#washing_lot').textbox('setValue', '');
            $('#washing_customer').textbox('setValue', '');
            $('#washing_barang').textbox('setValue', '');
            $('#washing_sub').textbox('setValue', '');
            $('#washing_lot').next().find('input').focus();
        });
       
        $('#washing_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotWashing').focus();
            }
        });
        
        $('#chlotWashing').bind('click', function(){
            $('#washing_sub').next().find('input').focus();
        });
        
        $('#washing_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#washing_oksave').focus();
            }
        });
    }
    
    function captureWashing() {
        Webcam.snap( function(data_uri) {
            $('#imgWashing').textbox('setValue',data_uri);
        });
    }
    
    function checkLotWashing() {
        var lotidWashing = $('#washing_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/washing/cekLot'); ?>',{washing_lot:lotidWashing},function(result){
            if (result.success){
                $('#washing_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/washing/getCustItem'); ?>',{washing_lot:lotidWashing},function(result){
                    $('#washing_customer').textbox('setValue', result.customer);
                    $('#washing_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#washing_lot').textbox('setValue', '');
                $('#washing_customer').textbox('setValue', '');
                $('#washing_barang').textbox('setValue', '');
                $('#washing_sub').textbox('setValue', '');
                $('#washing_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiWashingUpdate() {
        var row = $('#grid-transaksi_washing').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_washing-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_washing-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/washing/update'); ?>/' + row.washing_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiWashingSave(){
        $('#fm-transaksi_washing').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_washing').dialog('close');
                    //$('#grid-transaksi_washing').datagrid('reload');
                    transaksiWashingCreate();
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
    
    function transaksiWashingSaveEdit(){
        $('#fm-transaksi_washing-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_washing-edit').dialog('close');
                    $('#grid-transaksi_washing').datagrid('reload');
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
    
    function transaksiWashingHapus(){
        var row = $('#grid-transaksi_washing').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Washing '+row.washing_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/washing/delete'); ?>',{washing_id:row.washing_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_washing').datagrid('reload');
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
    
    function transaksiWashingImage(){
        var row = $('#grid-transaksi_washing').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/washing/viewImage'); ?>',{washing_id:row.washing_id},function(result){
                if (result.success){
                    var content = '<iframe scwashing="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.washing_id;
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

    function transaksiWashingSesdate()
    {
        $('#dlg-transaksi_washing_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_washing_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/washing/getDateWashing'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiWashingSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/washing/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_washing_sesdate').dialog('close');
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
    
    function transaksiWashingAfter()
    {
        $('#dlg-transaksi_washing-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Washing After');
        $('#fm-transaksi_washing-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/washing/updateAfter'); ?>';
    }
    
    function transaksiWashingAfterSave()
    {
        $('#fm-transaksi_washing-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_washing').datagrid('reload');
                    $('#dlg-transaksi_washing-after').dialog('close');
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
    
    function transaksiWashingBetween()
    {
        $('#dlg-transaksi_washing-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Washing Between');
        $('#fm-transaksi_washing-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/washing/updateBetween'); ?>';
    }
    
    function transaksiWashingBetweenSave()
    {
        $('#fm-transaksi_washing-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_washing').datagrid('reload');
                    $('#dlg-transaksi_washing-between').dialog('close');
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
    
    function transaksiWashingCheck()
    {
        $('#dlg-transaksi_washing-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_washing-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/washing/check'); ?>';
    }
    
    function transaksiWashingCheckSave()
    {
        $('#fm-transaksi_washing-check').form('submit',{
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
    #fm-transaksi_washing{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_washing-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_washing-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_washing-between{
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

<div id="dlg-transaksi_washing" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_washing">
    <form id="fm-transaksi_washing" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamWashing">
            </div>
        </div>
        <div class="center">
            <a id="captureWashing" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureWashing()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgWashing" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="washing_lot" name="washing_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotWashing" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotWashing()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="washing_sub" name="washing_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="washing_customer" name="washing_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="washing_barang" name="washing_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Washing</label>
                <input id="washing_date" name="washing_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_washing">
    <a href="javascript:void(0)" id="washing_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiWashingSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_washing').dialog('close');Webcam.reset();$('#grid-transaksi_washing').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_washing-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_washing-edit">
    <form id="fm-transaksi_washing-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="washing_lot" name="washing_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="washing_sub" name="washing_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Washing</label>
            <input type="text" id="washing_date" name="washing_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_washing-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiWashingSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_washing-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_washing_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_washing_sesdate">
    <form id="fm-transaksi_washing_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_washing_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiWashingSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_washing_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_washing-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_washing-after">
    <form id="fm-transaksi_washing-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Washing</label>
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
<div id="dlg-buttons-transaksi_washing-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiWashingAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_washing-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_washing-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_washing-between">
    <form id="fm-transaksi_washing-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Washing</label>
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
<div id="dlg-buttons-transaksi_washing-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiWashingBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_washing-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_washing-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_washing-check">
    <form id="fm-transaksi_washing-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Washing</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_washing-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiWashingCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_washing-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_washing.php -->
<!-- Location: ./application/views/transaksi/v_washing.php -->