<?php
if (isset($_POST["number"])) {
    $number = $_POST['number'];

    echo '<script>
    localStorage.setItem("row", ' . $number . ');
    </script>';
} else {
    $number = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
    <form>
        <table class="table table-bordered" id="fcfstable">
            <thead>
                <tr>
                    <th>Process ID</th>
                    <th>Burst Time</th>
                    <th>Arrival Time</th>
                    <th>Completion Time</th>
                    <th>Turn Around Time</th>
                    <th>Waiting Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php

                    echo '<script>';
                    echo 'var storedRow = localStorage.getItem("row");';
                    echo 'if(storedRow !== null && !isNaN(storedRow)) {';
                    echo '    var rowValue = parseInt(storedRow);';
                    echo '    if(rowValue > 0) {';
                    echo '        var number = rowValue;';
                    echo '    }';
                    echo '}';
                    echo '</script>';

                    for ($i = 1; $i <= $number; $i++) { // assuming there are 5 columns
                        echo '<tr>';
                        // Loop through each row
                        echo '<td><input type="text" name="process_id[' . $i . ']" placeholder="Process ID" value=' . "P" . $i . '></td>';
                        echo '<td><input type="text" name="burst_time[' . $i . ']" placeholder="Burst Time"></td>';
                        echo '<td><input type="text" name="arrival_time[' . $i . ']" placeholder="Arrival Time"></td>';
                        echo '<td><input type="text" name="completion_time[' . $i . ']" placeholder="Completion Time"></td>';
                        echo '<td><input type="text" name="turnaround_time[' . $i . ']" placeholder="Turn Around Time"></td>';
                        echo '<td><input type="text" name="waiting_time[' . $i . ']" placeholder="Waiting Time"></td>';
                        echo '</tr>';
                    }
                    ?>
                </tr>
            </tbody>
        </table>
        <button class="btn btn-primary form-control" id="computeButton">Compute</button>
    </form>
    <input type="text" id="printAT"></input>
    <button class="btn btn-danger" id="goBack" onclick="goBack()"> Back </button>
    <button class="btn btn-success" id="gantchart"> Generate </button>
</body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#computeButton').click(function(e) {
            const val = localStorage.getItem("row");

            var processIds = {};
            var burstTimes = {};
            var arrivalTimes = {};
            var completionTimes = {};
            var turnaroundTimes = {};
            var waitingTimes = {};

            for (var i = 1; i <= val; i++) {
                var processId = $("input[name='process_id[" + i + "]']").val();
                var burstTime = $("input[name='burst_time[" + i + "]']").val();
                var arrivalTime = $("input[name='arrival_time[" + i + "]']").val();
                var completionTime = $("input[name='completion_time[" + i + "]']").val();
                var turnaroundTime = $("input[name='turnaround_time[" + i + "]']").val();
                var waitingTime = $("input[name='waiting_time[" + i + "]']").val();

                processIds[i - 1] = processId;
                burstTimes[i - 1] = burstTime;
                arrivalTimes[i - 1] = arrivalTime;
                completionTimes[i - 1] = completionTime;
                turnaroundTimes[i - 1] = turnaroundTime;
                waitingTimes[i - 1] = waitingTime;
            }

            const payload = {
                processId: processIds,
                btime: burstTimes,
                atime: arrivalTimes,
                ctime: completionTimes,
                tatime: turnaroundTimes,
                wtime: waitingTimes,
                row: parseInt(localStorage.getItem('row')),
            };
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: 'compute.php',
                data: {
                    payload: JSON.stringify(payload)
                },
                success: function(response) {
                    // Kunin yung resposne from PHP
                    const load = JSON.parse(response);
                    // Extract yung object isa-isa
                    const artime = load.atime;
                    const btime = load.btime;
                    const pid = load.processId;
                    const row = load.row;

                    // arrival time
                    var arr1 = Object.values(artime).map(function(value) {
                        return [value];
                    });

                    // process ids
                    var ids = Object.values(pid).map(function(value) {
                        return [value];
                    });
                    // burst time 
                    var bt = Object.values(btime).map(function(value) {
                        return [value];
                    });

                    // var sortedAT = arr1.sort();
                    // console.log(sortedAT)

                    // For Completion, Turnaround, Waiting Time Results
                    var totalct = []; // Initialize totalsum array
                    var totaltat = [];
                    var totalwt = [];

                    for (var i = 0; i < row; i++) {
                        var currentCompletionTime = parseInt(arr1[i]) + parseInt(bt[i]);
                        var currentTATime = currentCompletionTime - parseInt(arr1[i]);
                        var currentWTime = currentCompletionTime - parseInt(bt[i]);

                        totalct.push(currentCompletionTime);
                        totaltat.push(currentTATime);
                        totalwt.push(currentWTime);

                        $("input[name='completion_time[" + (i + 1) + "]']").val(currentCompletionTime).show();
                        $("input[name='turnaround_time[" + (i + 1) + "]']").val(currentTATime).show();
                        $("input[name='waiting_time[" + (i + 1) + "]']").val(currentWTime).show();

                        for (var j = i + 1; j < bt.length; j++) {
                            currentCompletionTime += parseInt(bt[j]);
                            currentTATime = currentCompletionTime - parseInt(arr1[j]);
                            currentWTime = currentCompletionTime - parseInt(bt[j]);

                            totalct.push(currentCompletionTime);
                            totaltat.push(currentTATime);
                            totalwt.push(currentWTime);

                            $("input[name='completion_time[" + (j + 1) + "]']").val(currentCompletionTime).show();
                            $("input[name='turnaround_time[" + (j + 1) + "]']").val(currentTATime).show();
                            $("input[name='waiting_time[" + (j + 1) + "]']").val(currentTATime).show();


                        }
                        break;
                    }
                },
            });


        });


        $('#goBack').click(function(e) {
            window.location.href = "fcfs.php";
        });

        $('#gantchart').click(function(e) {
            e.preventDefault();
            // di ko pa nagagawa
        })
    });
</script>