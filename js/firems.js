/**
 * Created by alex on 12/26/15.
 */

var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_more').click(function() {
        $(this).before($("<div/>", {
            id: 'inputdiv'
        }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>File name</b></label>").append($("<input/>", {
            name: 'fileNames[]',
            type: 'text',
            id: 'fileName',
            class: 'form-control',
            placeholder: 'File name'
        }), $("<br/>"))

            .fadeIn('slow').append("<label><b>File number</b></label>").append($("<input/>", {
                name: 'fileNos[]',
                type: 'text',
                id: 'fileNo',
                class: 'form-control',
                placeholder: 'File number'
            }), $("<br/>"))

            .fadeIn('slow').append("").append($("<input/>", {
                name: 'file_source[]',
                type: 'hidden',
                id: 'file_source',
                class: 'form-control',
                value:'Offline File',
                placeholder: 'File number'
            }), $("<br/>"))



            .fadeIn('slow').append("<label><b>Request reason</b></label>").append($("<textarea/>", {
                name: 'requestReasons[]',
                type: 'textarea',
                id: 'requestReason',
                class: 'form-control',
                placeholder: 'Request reason'
            }), $("<br/>"))


        .fadeIn('slow').append("<label><b>Remark</b></label>").append($("<textarea/>", {
            name: 'requestorRemarks[]',
            type: 'textarea',
            id: 'requestorRemark',
            class: 'form-control',
            placeholder: 'Remark'
        }), $("<br/>")));



    });
    $('#upload').click(function(e) {
        var name = document.getElementById('fileNo').value;
        if (name == '' || name == null) {
            alert("Request at least one file");
            e.preventDefault();
            return false;
        }else{
            return true;
        }
    });

});





//function to upload multiple files requests

var def = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_more_perusal').click(function() {
        $(this).before($("<div/>", {
            id: 'perusalinputdiv'
        }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>File name</b></label>").append($("<input/>", {
                name: 'companyNames[]',
                type: 'text',
                id: 'companyName',
                class: 'form-control',
                placeholder: 'Company or business name'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>File number</b></label>").append($("<input/>", {
                name: 'fileNos[]',
                type: 'text',
                id: 'fileNo',
                class: 'form-control',
                placeholder: 'File number'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>File type</b></label>").append($("<select name='fileType[]' class='form-control'><option value='business'>Business name</option><option value='company'>Company name</option></select>", {
            }), $("<br/>"))


            .fadeIn('slow').append("<label><b>Remark</b></label>").append($("<textarea/>", {
                name: 'purposes[]',
                type: 'textarea',
                id: 'purpose',
                class: 'form-control',
                placeholder: 'Purpose'
            }), $("<br/>")));



    });
    $('#upload').click(function(e) {
        var name = document.getElementById('fileNo').value;
        if (name == '' || name == null) {
            alert("Request at least one file");
            e.preventDefault();
            return false;
        }else{
            return true;
        }
    });

});



//function to upload mult files from IP
var GHI = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#ip_add_more').click(function() {
        $(this).before($("<div/>", {
            id: 'ipinputdiv'
        }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>File name</b></label>").append($("<input/>", {
                name: 'fileNames[]',
                type: 'text',
                id: 'fileName',
                class: 'form-control',
                placeholder: 'File name'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>File type</b></label>").append($("<select name='fileType[]' id='fileTypes' class='form-control'><option value='TM'>TM</option><option value='SM'>SM</option><option value='QSM'>QSM</option><option value='QTM'>QTM</option><option value='AP/M'>AP/M</option><option value='TZ/T'>TZ/T</option><option value='TZ/S'>TZ/S</option><option value='TZ/P'>TZ/P</option></select>", {
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Year</b></label>").append($("<input/>", {
                name: 'year[]',
                type: 'text',
                class: 'form-control',
                placeholder: 'Year'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>File number</b></label>").append($("<input/>", {
                name: 'fileNos[]',
                type: 'text',
                id: 'fileNo',
                class: 'form-control',
                placeholder: 'File number'
            }), $("<br/>"))



            .fadeIn('slow').append("<label><b>Class</b></label>").append($("<input/>", {
                name: 'classes[]',
                type: 'text',
                id: 'class',
                class: 'form-control',
                placeholder: 'Class'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Mark/Owner</b></label>").append($("<input/>", {
                name: 'markOwners[]',
                type: 'text',
                id: 'markOwner',
                class: 'form-control',
                placeholder: 'Mark or owner'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Request reason</b></label>").append($("<textarea/>", {
                name: 'requestReasons[]',
                type: 'textarea',
                id: 'requestReason',
                class: 'form-control',
                placeholder: 'Request reason'
            }), $("<br/>"))


            .fadeIn('slow').append("<label><b>Remark</b></label>").append($("<textarea/>", {
                name: 'requesterRemarks[]',
                type: 'textarea',
                id: 'requesterRemark',
                class: 'form-control',
                placeholder: 'Remark'
            }), $("<br/>")));



    });
    $('#ip_upload').click(function(e) {
        var fname = document.getElementById('fileNo').value;
        if (fname == '' || fname == null) {
            alert("Request at least one file");
            e.preventDefault();
            return false;
        }else{
            return true;
        }
    });

});



// start cl files
var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_more_refusal_reason').click(function() {
        $(this).before($("<div/>", {
            id: 'refusalreason'
        }).fadeIn('slow').append("<label><b>Refusal reason</b></label>").append($("<textarea/>", {
                name: 'refusal_reasons[]',
                type: 'textarea',
                id: 'refusal_reason',
                class: 'form-control',
                placeholder: 'Remark',
                rows : '2'
            }), $("<br/>")));



    });

});


var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_cl').click(function() {
        $(this).before($("<div/>", {
            id: 'inputdiv'
        }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>Receipt number</b></label>").append($("<input/>", {
                name: 'receipt_number[]',
                type: 'text',
                id: 'receipt_number',
                class: 'form-control',
                placeholder: 'Receipt number'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>File number</b></label>").append($("<input/>", {
                name: 'fileNos[]',
                type: 'text',
                id: 'fileNo',
                class: 'form-control',
                placeholder: 'File number'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>File name</b></label>").append($("<input/>", {
                name: 'fileNames[]',
                type: 'text',
                id: 'fileNames',
                class: 'form-control',
                placeholder: 'File name'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Activity</b></label>").append($("<select name='activity[]' id='activity' class='form-control'><option value='business_name'>Business name</option><option value='change_of_name'>Change of name</option><option value='dispense'>Dispense</option><option value='amendment'>Amendment</option><option value='struck_off'>Struck off</option><option value='copy_of_certificate_or_memart'>Copy of certificate or memart</option><option value='certified_document'>Certified document</option></select>", {
            }), $("<br/>"))



            .fadeIn('slow').append("<label><b>Refusal reason</b></label>").append($("<textarea/>", {
                name: 'refusal_reasons[]',
                type: 'textarea',
                id: 'refusal_reason',
                class: 'form-control',
                placeholder: 'Refusal reason'
            }), $("<br/>"))


            .fadeIn('slow').append("<label><b>Status</b></label>").append($("<select name='status[]' id='status' class='form-control'><option value='refused'>Refused</option></select>", {
            }), $("<br/>"))




        );



    });

});


var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_more_file').click(function() {
        $(this).before($("<div/>", {
                id: 'inputdiv'
            }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>File name</b></label>").append($("<input/>", {
                name: 'fileNames[]',
                type: 'text',
                id: 'fileName',
                class: 'form-control',
                placeholder: 'File name'
            }), $("<br/>"))

                .fadeIn('slow').append("<label><b>File number</b></label>").append($("<input/>", {
                    name: 'fileNos[]',
                    type: 'text',
                    id: 'fileNo',
                    class: 'form-control',
                    placeholder: 'File number'
                }), $("<br/>"))

                .fadeIn('slow').append("<label><b>Date issued</b></label>").append($("<input/>", {
                    name: 'date_issued[]',
                    type: 'text',
                    id: 'date_issued',
                    class: 'form-control datepicker',
                    placeholder: 'Date issued'
                }), $("<br/>"))


                .fadeIn('slow').append("<label><b>Reason</b></label>").append($("<select name='subjects[]' class='form-control'><option value='Official search'>Official search</option><option value='Struck off'>Struck off</option><option value='Debenture'>Debenture</option><option value='Change of name'>Change of name</option><option value='Amendment'>Amendment</option><option value='Certification 14 ab&b'>Certification 14 ab&b</option><option value='perusal'>Perusal</option><option value='association'>Association</option><option value='disclaimer'>Disclaimer</option><option value='registration'>Registration</option><option value='renewal'>Renewal</option><option value='advertisement'>Advertisement</option><option value='Change of address'>Change of address</option></select>", {
                    type: 'textatera',
                    id: 'subject',
                    class: 'form-control',
                    placeholder: 'Reason'
                }), $("<br/>"))

                .fadeIn('slow').append("<label><b>Sent to</b></label>").append($("<input/>", {
                    name: 'action_officer_names[]',
                    type: 'text',
                    id: 'action_officer_name',
                    class: 'form-control',
                    placeholder: 'Action officer name'
                }), $("<br/>"))








        );



    });

});



var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_conf_file').click(function() {
        $(this).before($("<div/>", {
                id: 'conf_file'
            }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>File name</b></label>").append($("<input/>", {
                name: 'fileNames[]',
                type: 'text',
                id: 'fileName',
                class: 'form-control',
                placeholder: 'File name'
            }), $("<br/>"))

                .fadeIn('slow').append("<label><b>File number</b></label>").append($("<input/>", {
                    name: 'fileNos[]',
                    type: 'text',
                    id: 'fileNo',
                    class: 'form-control',
                    placeholder: 'File number'
                }), $("<br/>"))







        );



    });

});

var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_out_file').click(function() {
        $(this).before($("<div/>", {
                id: 'add_out_file'
            }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>File name</b></label>").append($("<input/>", {
                name: 'fileNames[]',
                type: 'text',
                id: 'fileName',
                class: 'form-control',
                placeholder: 'File name'
            }), $("<br/>"))

                .fadeIn('slow').append("<label><b>File number</b></label>").append($("<input/>", {
                    name: 'fileNos[]',
                    type: 'text',
                    id: 'fileNo',
                    class: 'form-control',
                    placeholder: 'File number'
                }), $("<br/>"))

                .fadeIn('slow').append("<label><b>Date issued</b></label>").append($("<input/>", {
                    name: 'date_out[]',
                    type: 'text',
                    id: 'date_out',
                    class: 'form-control datepicker',
                    placeholder: 'Date out'
                }), $("<br/>"))

                .fadeIn('slow').append("<label><b>Name of signed officer</b></label>").append($("<input/>", {
                    name: 'officer_signed[]',
                    type: 'text',
                    id: 'officer_signed',
                    class: 'form-control',
                    placeholder: 'Signed officer'
                }), $("<br/>"))

                .fadeIn('slow').append("<label><b>Institution</b></label>").append($("<input/>", {
                    name: 'institution[]',
                    type: 'text',
                    id: 'institution',
                    class: 'form-control',
                    placeholder: 'Institution'
                }), $("<br/>"))








        );



    });

});


var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_doc').click(function() {
        $(this).before($("<div/>", {
                id: 'add_document'
            }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>File name</b></label>").append($("<input/>", {
                name: 'fileNames[]',
                type: 'text',
                id: 'fileName',
                class: 'form-control',
                placeholder: 'File name'
            }), $("<br/>"))

                .fadeIn('slow').append("<label><b>File number</b></label>").append($("<input/>", {
                        name: 'fileNos[]',
                    type: 'text',
                    id: 'fileNo',
                    class: 'form-control',
                    placeholder: 'File number'
                }), $("<br/>"))

                .fadeIn('slow').append("<label><b>Document types(Separate by comma)</b></label>").append($("<textarea/>", {
                    name: 'document_type[]',
                    type: 'textarea',
                    id: 'document_type',
                    class: 'form-control',
                    placeholder: 'Document type'
                }), $("<br/>"))

                .fadeIn('slow').append("<label><b>Date received</b></label>").append($("<input/>", {
                    name: 'date_received[]',
                    type: 'text',
                    id: 'date_received',
                    class: 'form-control',
                    placeholder: 'Date received'
                }), $("<br/>"))

                .fadeIn('slow').append("<label><b>Date issued</b></label>").append($("<input/>", {
                    name: 'date_issued[]',
                    type: 'text',
                    id: 'date_issued',
                    class: 'form-control',
                    placeholder: 'Date issued'
                }), $("<br/>"))








        );



    });

});


var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_more_tasks').click(function() {
        $(this).before($("<div/>", {
                id: 'add_tasks'
            }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>Task</b></label>").append($("<textarea/>", {
                name: 'tasks[]',
                type: 'text',
                id: 'tasks',
                class: 'form-control',
                placeholder: 'Task name'
            }), $("<br/>"))

        );

    });

});

var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_incoming_letter').click(function() {
        $(this).before($("<div/>", {
            id: 'inputdiv'
        }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>Date Received</b></label>").append($("<input/>", {
            name: 'date_received[]',
            type: 'text',
            id: 'date_received',
            class: 'form-control',
            placeholder: 'Date Received'
        }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Date On Letter</b></label>").append($("<input/>", {
                name: 'date_on_letter[]',
                type: 'text',
                id: 'date_on_letter',
                class: 'form-control',
                placeholder: 'Date On letter'
            }), $("<br/>"))


            .fadeIn('slow').append("<label><b>Reference On Letter</b></label>").append($("<textarea/>", {
                name: 'reference_on_letter[]',
                type: 'text',
                id: 'reference_on_letter',
                class: 'form-control',
                placeholder: 'Reference On Letter'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>From Whom</b></label>").append($("<textarea/>", {
                name: 'from_whom[]',
                type: 'text',
                id: 'from_whom',
                class: 'form-control',
                placeholder: 'From Whom'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Subject</b></label>").append($("<textarea/>", {
                name: 'subject[]',
                type: 'textarea',
                id: 'subject',
                class: 'form-control',
                placeholder: 'Subject'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Letter Location</b></label>").append($("<select name='letter_location[]' id='fileTypes' class='form-control'><option value='administration'>Administration</option><option value='commercial_law'>Commercial Law</option><option value='industrial_license'>Industrial License</option><option value='intellectual_property'>Intellectual Property</option></select>", {
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Address</b></label>").append($("<textarea/>", {
                name: 'address[]',
                type: 'text',
                id: 'address',
                class: 'form-control',
                placeholder: 'Address'
            }), $("<br/>")));




    });

});

var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_outgoing_letter').click(function() {
        $(this).before($("<div/>", {
            id: 'inputdiv'
        }).fadeIn('slow').append("<hr>").append("<br>").append("<label><b>Date received for dispatch</b></label>").append($("<input/>", {
            name: 'date_for_dispatch[]',
            type: 'text',
            id: 'date_for_dispatch',
            class: 'form-control',
            placeholder: 'Date received for dispatch'
        }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Date dispatched</b></label>").append($("<input/>", {
                name: 'date_dispatched[]',
                type: 'text',
                id: 'date_dispatched',
                class: 'form-control',
                placeholder: 'Date dispatched'
            }), $("<br/>"))


            .fadeIn('slow').append("<label><b>Reference On Letter</b></label>").append($("<textarea/>", {
                name: 'reference_on_letter[]',
                type: 'text',
                id: 'reference_on_letter',
                class: 'form-control',
                placeholder: 'Reference On Letter'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>To Whom</b></label>").append($("<textarea/>", {
                name: 'to_whom[]',
                type: 'text',
                id: 'to_whom',
                class: 'form-control',
                placeholder: 'To Whom'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Subject</b></label>").append($("<textarea/>", {
                name: 'subject[]',
                type: 'textarea',
                id: 'subject',
                class: 'form-control',
                placeholder: 'Subject'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Dispatch method</b></label>").append($("<select name='dispatch_method[]' id='fileTypes' class='form-control'><option value='post_office'>Post office</option><option value='dispatch_reg_number'>Dispatch Reg Number</option></select>", {
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Address</b></label>").append($("<textarea/>", {
                name: 'address[]',
                type: 'textarea',
                id: 'address',
                class: 'form-control',
                placeholder: 'Address'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Letter status</b></label>").append($("<select name='letter_status[]' id='fileTypes' class='form-control'><option value='dispatched'>Dispatched</option><option value='not_dispatched'>Not dispatched</option></select>", {
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>Dispatch number</b></label>").append($("<input/>", {
                name: 'dispatch_number[]',
                type: 'text',
                id: 'dispatch_number',
                class: 'form-control',
                placeholder: 'Dispatch number'
            }), $("<br/>")));




    });

});

var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {
//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
    $('#add_confidential_letter').click(function() {
        $(this).before($("<div/>", {
            id: 'inputdiv'
        }).fadeIn('slow').append("<label><hr><b>Reference On Letter</b></label>").append($("<textarea/>", {
                name: 'reference_on_letter[]',
                type: 'text',
                id: 'reference_on_letter',
                class: 'form-control',
                placeholder: 'Reference On Letter'
            }), $("<br/>"))

            .fadeIn('slow').append("<label><b>From Whom</b></label>").append($("<textarea/>", {
                name: 'from_whom[]',
                type: 'text',
                id: 'from_whom',
                class: 'form-control',
                placeholder: 'From Whom'
            }), $("<br/>")));

    });

});









//end cl files


