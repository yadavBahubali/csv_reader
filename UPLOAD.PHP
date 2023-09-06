<?php
include 'database.php';

if (isset($_POST['submit'])) {
    $upload = $_FILES['upload']['tmp_name'];

    // Check if a file was uploaded
    if (empty($upload)) {
        echo "Please select a file to upload.";
    } else {
        // Check if the uploaded file is a valid CSV
        $file_info = pathinfo($_FILES['upload']['name']);
        if (strtolower($file_info['extension']) !== 'csv') {
            echo "Invalid file format. Please upload a CSV file.";
        } else {
            if (($handle = fopen($upload, 'r')) !== false) {
                $n = 1;
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    if ($n === 1) {
                        // Skip the first line (column headings)
                        $n++;
                        continue;
                    }

                    // Sanitize and use prepared statements for SQL query
                    $sql = "INSERT INTO csv_data (id_no,first_name, last_name, email, phone_no) VALUES (?,?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);

                    if (!$stmt) {
                        echo "Error in SQL statement: " . mysqli_error($conn);
                    } else {
                        // Bind parameters and execute the statement
                        mysqli_stmt_bind_param($stmt, 'isssi', $data[0], $data[1], $data[2], $data[3],$data[4] );
                        if (mysqli_stmt_execute($stmt)) {
                            
                        } else {
                            echo "Error inserting data: " . mysqli_error($conn);
                        }

                        mysqli_stmt_close($stmt);
                    }

                    $n++;
                }
                fclose($handle);
                echo"data inserted";
            } else {
                echo "Error opening the uploaded file.";
            }
        }
    }
}
?>
