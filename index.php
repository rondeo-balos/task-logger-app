<?php

define( 'ABSPATH', dirname(__FILE__) . '/' );

require 'vendor/autoload.php';
require_once 'connection.php';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Logger App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://zeptojs.com/zepto.min.js"></script>
</head>
<body>

    <div class="conainer-fluid">
        <div class="d-flex flex-row justify-content-evenly">
            <div class="col-2">
                <div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark bg-body-tertiary shadow" style="width: 280px; min-height: 100vh">
                    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-4">Task Logger App</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link active" aria-current="page">
                                <i class="fa-regular fa-clock"></i>
                                Time Tracker
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white">
                                <i class="fa-regular fa-calendar-days"></i>
                                Timesheet
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white">
                                <i class="fa-solid fa-tag"></i>
                                Tags
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-10 p-5">
                <div class="border mb-5 bg-body-tertiary p-2">
                    <form action="/api/tasks" method="POST" id="taskForm">
                        <div class="d-flex flex-row justify-content-between">
                            <div class="col-7">
                                <input type="text" name="title" class="form-control border-0 rounded-0" placeholder="What are you working on?" required>
                            </div>
                            <input type="hidden" name="description" value="[]">
                            <div class="col-auto">
                                <button type="button" role="button" class="btn btn-outline-primary border-0 rounded-0" data-bs-toggle="modal" data-bs-target="#modalDescription"><i class="fa fa-list"></i></button>
                            </div>
                            <div class="col-1">
                                <div class="dropdown tags-dropdown">
                                    <a href="#" class="btn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"><i class="fa fa-tag"></i> Tag</a>
                                    <div class="dropdown-menu p-4" style="min-width: 300px"></div>
                                </div>
                            </div>
                            <input type="hidden" name="start">
                            <input type="hidden" name="end">
                            <div class="col-auto">
                                <div class="d-flex h-100 align-items-center justify-content-center">
                                    <strong id="timer">00:00:00</strong>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex h-100 align-items-center justify-content-center">
                                    <button type="button" role="button" class="btn btn-primary rounded-0" id="start"><i class="fa fa-play"></i> START</button>
                                    <button type="submit" role="submit" class="btn btn-danger rounded-0 d-none" id="end"><i class="fa fa-stop"></i> STOP</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="data"></div>
                
                <div class="border rounded d-none shadow mb-4 table-template">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <td colspan="500" class="bg-body-secondary">
                                    <div class="d-flex flex-row justify-content-between px-2">
                                        <strong id="date">[Date]</strong>
                                        <span id="dayTotal">TOTAL: <strong>[total]</strong></span>
                                    </div>
                                </td>
                            </tr>
                        </thead>

                        <tbody></tbody>
                    </table>
                </div>

                <div class="modal fade" id="modalDescription" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tasks Description</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <textarea class="description form-control mb-3 d-none"></textarea>
                                <div class="descriptionWrapper"></div>
                                <div class="text-end">
                                    <button class="btn btn-outline-primary rounded-0 addDescription"><i class="fa fa-plus-circle"></i> Description</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $( document ).ready(function($){
            const generateTags = function( dropdown, idAfter = '', currVal = 0, callback = function(){} ) {
                $.getJSON( '/api/tags', function(response) {
                    dropdown.empty();
                    response.forEach(tag => {
                        var input = $( `<input type="radio" class="btn-check" name="tag" data-title="${tag.tag}" data-color="${tag.color}" id="tag-${tag.tag}-${idAfter}" value="${tag.ID}" autocomplete="off" required>` );
                        var label = $( `<label class="btn btn-sm btn-outline-${tag.color} m-1" for="tag-${tag.tag}-${idAfter}">${tag.tag}</label>` );

                        dropdown.append( input ).append( label );
                    });

                    callback();
                });
            }
            var dropdown = $( '.dropdown.tags-dropdown .dropdown-menu' );
            generateTags( dropdown );

            $( document ).on( 'change', '[name="tag"]', function(){
                var title = $(this).attr( 'data-title' );
                var color = $(this).attr( 'data-color' );
                $(this).closest( '.dropdown.tags-dropdown' ).find( 'a' ).html( `<i class="fa fa-tag"></i> ${title}` ).addClass( `text-${color}` );
            });

            const addDescription = function( val ) {
                var description = $('.description:not(.cloned)').clone();
                description.addClass( 'cloned' );
                description.removeClass( 'd-none' );
                description.val(val);

                $('.descriptionWrapper').append( description );
            }
            $( '.addDescription' ).on( 'click', function(e) {
                e.preventDefault();
                addDescription( '' );
            });
            var globalForm = null;
            $(document).on( 'click', '[data-bs-target="#modalDescription"]', function() {
                $( '.descriptionWrapper' ).empty();
                globalForm = $(this).closest( 'form' );
                var description = globalForm.find( '[name="description"]' ).val();
                try {
                    description = JSON.parse( description );
                    description.forEach(desc => {
                        addDescription( desc );
                    });
                } catch( $e ) {
                    console.log( 'description', $e );
                }
            });
            $( document ).on( 'keyup change paste', '.modal .description.cloned', function() {
                var description = [];
                $('.modal .description.cloned').each( function(index) {
                    description.push( $(this).val() );
                });
                globalForm.find( '[name="description"]' ).val( JSON.stringify( description ) );
                globalForm.find( '[name="description"]' ).change();
            });
            const deleteData = function(ID) {
                $.ajax({
                    type: 'DELETE',
                    url: `api/tasks/${ID}`,
                    data: [],
                    success: function(data) {
                        generateData();
                    }
                });
            }
            const generateData = function() {
                var dataWrapper = $('#data');
                dataWrapper.empty();

                $.getJSON('/api/tasks', function(response) {

                    // Iterate week
                    Object.keys(response).reverse().forEach(week => {
                        var data = response[week];

                        // Iterate days
                        Object.keys(data).forEach( day => {
                            var table = $('.table-template').clone();
                            table.removeClass( 'd-none' ).removeClass( 'table-template' );
                            table.find( 'tbody' ).empty();

                            var tasks = data[day];
                            var totalSeconds = 0;
                            
                            // Iterate tasks for the day
                            tasks.forEach( task => {
                                var seconds = task.end - task.start;
                                totalSeconds += seconds;

                                var row = $('<tr>');
                                row.append( '<td>' );

                                var form = $(`<form method="POST" action="/api/tasks/${task.ID}" class="d-flex flex-row justify-content-between align-items-center w-100" style="gap: 10px;">`);
                                
                                var title = $('<input type="text" name="title" class="form-control border-0">').val( task.title );
                                var description = $('<input type="hidden" name="description">').val( task.description );
                                var descriptionBtn = $( '<button type="button" role="button" class="btn btn-outline-primary border-0 rounded-0" data-bs-toggle="modal" data-bs-target="#modalDescription">' ).html( '<i class="fa fa-list"></i>' );
                                var tag = $( '<div class="dropdown tags-dropdown col-1">' )
                                    .append( '<a href="#" class="btn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"><i class="fa fa-tag"></i> Tag</a>' )
                                    .append( '<div class="dropdown-menu p-4" style="min-width: 300px">' );
                                generateTags( tag.find( '.dropdown-menu' ), task.ID, task.tag, function(){
                                    tag.find( `[value="${task.tag}"]` ).attr( 'checked', true ).change();
                                });
                                var start_raw = $('<input type="datetime-local" step="1" name="start_raw" class="form-control border-0">').val( formatDateTime(task.start*1000) );
                                var end_raw = $('<input type="datetime-local" step="1" name="end_raw" class="form-control border-0">').val( formatDateTime(task.end*1000) );
                                var start = $('<input type="hidden" name="start">').val( task.start );
                                var end = $('<input type="hidden" name="end">').val( task.end );
                                var total = $('<span id="timer">').text( formatElapsedTime(seconds) );
                                var deleteBtn = $('<button class="btn btn-outline-danger btn-sm border-0">').attr('data-id', task.ID).html( '<i class="fa fa-trash">' );
                                
                                deleteBtn.on( 'click', function(e) {
                                    e.preventDefault();
                                    deleteData(task.ID);
                                });

                                form.on( 'keyup change paste', 'input, select, textarea', function(){
                                    start.val( parseDateTimeLocalToSeconds(start_raw.val()) );
                                    end.val( parseDateTimeLocalToSeconds(end_raw.val()) );
                                    updateData( form, function() {
                                        var new_seconds = end.val() - start.val();
                                        var difference = new_seconds - seconds;
                                        totalSeconds += difference;
                                        form.find( '#timer' ).text( formatElapsedTime(new_seconds) );
                                        table.find( '#dayTotal' ).html( `Total: <strong>${formatElapsedTime(totalSeconds)}</strong>` );
                                    });
                                });

                                form.append( $( '<div class="col-6">' ).append(title) )
                                    .append( description )
                                    .append( descriptionBtn )
                                    .append( tag )
                                    .append( start_raw )
                                    .append( ' - ' )
                                    .append( end_raw )
                                    .append( total )
                                    .append( deleteBtn )
                                    .append( start )
                                    .append( end );
                                row.find( 'td' ).append( form );

                                table.find( 'tbody' ).append( row );
                            });

                            table.find( '#date' ).html( getDayName(day) );
                            table.find( '#dayTotal' ).html( `Total: <strong>${formatElapsedTime(totalSeconds)}</strong>` );
                            table.addClass( 'animate__animated animate__fadeIn' );
                            dataWrapper.append( table );

                            //$(document).find( 'input[name="tag"]:checked' ).change();
                        });
                    });
                    if( response.length <= 0 ) {
                        dataWrapper.html('<center>Wow! Such empty<center>');
                    }
                });
            }
            generateData();

            var interval = null;

            $( '#start' ).on( 'click', function(e) {
                e.preventDefault();
                $('#start').toggleClass('d-none');
                $('#end').toggleClass('d-none');

                $( '#taskForm' ).find('[name="start"]').val(Math.floor(Date.now() / 1000)); // start timer

                interval = setInterval(() => {
                    var start = $( '#taskForm' ).find( '[name="start"]' ).val();
                    var timer = Math.floor(Date.now()/1000) - Math.floor(start);
                    $( '#timer' ).text( formatElapsedTime(timer) );
                }, 1000);
            });

            const updateData = function(form, callback) {
                var method = form.attr('method');
                var actionUrl = form.attr('action');

                $.ajax({
                    type: method,
                    url: actionUrl,
                    data: form.serialize(),
                    success: function(data) {
                        callback();
                    }
                });
            }

            $( '#taskForm' ).submit(function(e) {
                e.preventDefault();

                var form = $(this);
                form.find('[name="end"]').val(Math.floor(Date.now() / 1000)); // end timer
                
                updateData( form, function() {
                    generateData();
                    $('#start').toggleClass('d-none');
                    $('#end').toggleClass('d-none');
                    form[0].reset();
                    clearInterval( interval );
                    $( '#timer' ).text( '00:00:00' )
                });

                return false;
            });
        });

        function formatDateTime(timestamp) {
            var date = new Date(timestamp);
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var day = String(date.getDate()).padStart(2, '0');
            var hours = String(date.getHours()).padStart(2, '0');
            var minutes = String(date.getMinutes()).padStart(2, '0');
            var seconds = String(date.getSeconds()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
        }
        // Function to format seconds into hh:mm:ss
        function formatElapsedTime(seconds) {
            const hrs = Math.floor(seconds / 3600).toString().padStart(2, '0');
            const mins = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
            const secs = (seconds % 60).toString().padStart(2, '0');
            return `${hrs}:${mins}:${secs}`;
        }
        // Function to convert numeric day to day name or special cases (Today, Yesterday)
        function getDayName(day) {
            // Create a new Date object for the current month and year, and set the day
            const date = new Date();
            const currentYear = date.getFullYear();
            const currentMonth = date.getMonth();

            // Set the provided day
            date.setDate(day);

            // Get today's date and yesterday's date
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(today.getDate() - 1);

            // Check if the provided day is today
            if (date.getFullYear() === today.getFullYear() && date.getMonth() === today.getMonth() && date.getDate() === today.getDate()) {
                return "Today";
            }

            // Check if the provided day is yesterday
            if (date.getFullYear() === yesterday.getFullYear() && date.getMonth() === yesterday.getMonth() && date.getDate() === yesterday.getDate()) {
                return "Yesterday";
            }

            // Array of day names
            const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

            // Get the day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
            const dayOfWeek = date.getDay();

            // Return the corresponding day name
            return days[dayOfWeek];
        }
        // Function to convert datetime-local string to Unix timestamp in seconds
        function parseDateTimeLocalToSeconds(dateTimeLocal) {
            // Parse the datetime-local string into a Date object
            const date = new Date(dateTimeLocal);
            // Convert the Date object to milliseconds since the Unix epoch and then to seconds
            return Math.floor(date.getTime() / 1000);
        }
    </script>

    <style>
        .table {
            margin-bottom: 4px;
        }
    </style>
</body>
</html>