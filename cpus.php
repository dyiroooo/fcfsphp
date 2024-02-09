<!DOCTYPE html>
<html>
<head>
    <title>FCFS Scheduling</title>
</head>
<body>
    <h2>First Come First Serve (FCFS) Scheduling Algorithm</h2>
    <form method="post">
        <label for="process_count">Enter Number of Processes:</label>
        <input type="number" id="process_count" name="process_count" required><br><br>

        <div id="process_inputs"></div>

        <input type="submit" value="Submit">
    </form>

    <?php
    function calculateCompletionTime($burstTimes) {
        $completionTime = array();
        $sum = 0;
        foreach ($burstTimes as $burstTime) {
            $sum += $burstTime;
            $completionTime[] = $sum;
        }
        return $completionTime;
    }

    function calculateTurnaroundTime($arrivalTimes, $completionTimes) {
        $turnaroundTime = array();
        for ($i = 0; $i < count($arrivalTimes); $i++) {
            $turnaroundTime[] = $completionTimes[$i] - $arrivalTimes[$i];
        }
        return $turnaroundTime;
    }

    function calculateWaitingTime($turnaroundTimes, $burstTimes) {
        $waitingTime = array();
        for ($i = 0; $i < count($turnaroundTimes); $i++) {
            $waitingTime[] = $turnaroundTimes[$i] - $burstTimes[$i];
        }
        return $waitingTime;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $process_count = $_POST['process_count'];
        ?>
        <h3>Processes Details</h3>
        <table border="1">
            <tr>
                <th>Process</th>
                <th>Arrival Time</th>
                <th>Burst Time</th>
                <th>Completion Time</th>
                <th>Turnaround Time</th>
                <th>Waiting Time</th>
            </tr>
            <?php
            $arrival_times = $_POST['arrival_time'];
            $burst_times = $_POST['burst_time'];
            $completion_times = calculateCompletionTime($burst_times);
            $turnaround_times = calculateTurnaroundTime($arrival_times, $completion_times);
            $waiting_times = calculateWaitingTime($turnaround_times, $burst_times);

            for ($i = 0; $i < $process_count; $i++) {
                ?>
                <tr>
                    <td><?php echo "P" . ($i + 1); ?></td>
                    <td><?php echo $arrival_times[$i]; ?></td>
                    <td><?php echo $burst_times[$i]; ?></td>
                    <td><?php echo $completion_times[$i]; ?></td>
                    <td><?php echo $turnaround_times[$i]; ?></td>
                    <td><?php echo $waiting_times[$i]; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    }
    ?>
    <script>
        // JavaScript to dynamically generate input fields for arrival time and burst time based on process count
        document.getElementById("process_count").addEventListener("change", function () {
            var process_count = parseInt(this.value);
            var process_inputs = document.getElementById("process_inputs");
            process_inputs.innerHTML = ""; // Clear previous inputs

            for (var i = 0; i < process_count; i++) {
                var arrival_input = document.createElement("input");
                arrival_input.type = "number";
                arrival_input.name = "arrival_time[]";
                arrival_input.placeholder = "Arrival Time for P" + (i + 1);
                arrival_input.required = true;

                var burst_input = document.createElement("input");
                burst_input.type = "number";
                burst_input.name = "burst_time[]";
                burst_input.placeholder = "Burst Time for P" + (i + 1);
                burst_input.required = true;

                process_inputs.appendChild(arrival_input);
                process_inputs.appendChild(burst_input);
                process_inputs.appendChild(document.createElement("br"));
            }
        });
    </script>
</body>
</html>
