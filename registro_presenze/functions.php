<?php
require "vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// saves in a text file the error messages
function log_error($error_message) {
    $log_file = 'error_log.txt';

    if (!file_exists($log_file)) {
        touch($log_file);
    }
    $log_entry = date('Y-m-d H:i:s').' -- Error: '.$error_message.PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}


// prints a js script that displays a calendar with clock
function clock_script() {
    echo "
        <script> 
        function clock() {
            var now = new Date();
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');
            var seconds = now.getSeconds().toString().padStart(2, '0');
            var day = now.getDate().toString().padStart(2, '0');
            var month = (now.getMonth() + 1).toString().padStart(2, '0');
            var year = now.getFullYear();

            var formattedDate = day + '-' + month + '-' + year;
            var formattedTime = hours + ':' + minutes + ':' + seconds;
            document.getElementById('now').innerText = 'Sono le ' + formattedTime + ' del ' + formattedDate;
        }
        // updates clock every second
        setInterval(now, 1000);
        </script>
    ";
}


// prints a js script that manages check and uncheck of presence check box
function set_input_field_script() {
    echo "
        <script>
        function set_input_field(checkbox) {
            // reads parent line 'tr'
            var row = checkbox.closest('tr');
        
            // defines fields to modify
            var absentCheckbox = row.querySelector('#absent');
            var timeInput = row.querySelector('#time');
            var noteInput = row.querySelector('#note');
        
            // the check is different from initial check
            var isChecked = checkbox.checked !== checkbox.defaultChecked;
        
            // enables or disables inputs based on the checkbox status
            timeInput.disabled = !isChecked;
        
            // on checkbox variation, enables field time
            if (isChecked) {
                timeInput.disabled = false;
                // and enables note field only if 'absent' is checked
                if (absentCheckbox.checked) {
                    noteInput.disabled = false;
                }
            } else {
                // if returns to the default position, disables 'time' and 'note'
                timeInput.disabled = true;
                noteInput.disabled = true;
            }
        }
        </script>
    ";
}


// creates 2 buttons for pdf and xlsx files
function create_save_button($array, $title){
    if(!empty($array)){
        $html = "";
        // crea pulsante per salvare la tabella
        $html .= 
                "<form action='save_document.php' method='post' class='table'>
                    <input type='hidden' name='title' value='".htmlspecialchars($title)."'>
                    <input type='hidden' name='array' value='".htmlspecialchars(json_encode($array))."'>
                    <span class='table' id='save_buttons'>
                        <input class='button' type='submit' name='pdf' value='Salva PDF'>
                        <input class='button' type='submit' name='xlsx' value='Esporta XLSX'>
                    </span>
                </form>";
        return $html;
    }
}


// returns an html table of an received array
function array_to_table($array){
    $html = "";

    if (!empty($array)){     
        $html .= "<table>";    
        $html .= "<tr>";
        foreach ($array[0] as $key => $value) {
            $key = str_replace("_", " ", $key);
            $key = strtoupper($key);
            $html .= "<th>$key</th>";
        }
        $html .= "</tr>";
        
        foreach ($array as $row) {
            $html .= "<tr>";
            foreach ($row as $value) {
                $value = str_replace("_", " ", $value);
                $value = ucfirst($value);
                $html .= "<td>$value</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</table>";

    } else {
        $html .= "<p>&nbsp;Nessun corso risultante.</p><p></p>";
    }
    return $html;
}


// prints a page section with a title, a table array and the save buttons
function create_section($array, $title){
    echo 
        "<div class='table'>
            <h6 class='table'>".$title."</h6>
        </div>
    ";
    echo create_save_button($array, $title);
    echo array_to_table($array);
}


// creates a pdf file from a table and a title
function save_pdf($table, $title) {
    try {
        $options = new Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf\Dompdf($options);

        $html = "<style>
                    table {
                        margin: 5% auto;
                        width: 100%;
                    }
                    th, td {
                        border: 1px solid #11324D;
                        padding: 1% 2%;
                        text-align: center;
                        vertical-align: middle; 
                    }
                    th {
                        background-color: #C1CFC0;
                    }
                    tr:nth-child(even) {
                        background-color: #FFFFFF;
                    }
                    tr:nth-child(odd) {
                        background-color: #F2F5F2;
                    }
                    h5 {
                        font-size: 16px;
                        text-align: center;
                    }
                </style>";

        $html .= "<h5>".$title."</h5>";
        $html .= $table;

        // loads the HTML into Dompdf
        $dompdf->loadHtml($html);
        // renders the HTML into a PDF file
        $dompdf->render();
        // views the PDF as a stream and forces it to download with a specific name
        $dompdf->stream(str_replace(' ', '_', $title).'.pdf');
        exit;
    } catch (Exception $e) {
        // saves error log
        log_error($e->getMessage());
        echo "<p>Si è verificato un errore durante la generazione del PDF.</p>";
        exit;
    }
}


// creates an xlsx file from an array and a title
function export_xlsx($array, $title) {
    try {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // columns header
        $row = 1;
        $header = array_keys(current($array));
        $col = 'A';
        foreach ($header as $columnHeader) {

            // transforms to uppercase and removes dashes
            $sheet->setCellValue($col . $row, strtoupper(str_replace('_', ' ', $columnHeader)));
            // transforms to bold
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            // adapts width to content
            $sheet->getColumnDimension($col)->setAutoSize(true);
            // centers text
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $col++;
        }
        // values
        $row++;
        foreach ($array as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $sheet->getColumnDimension($col)->setAutoSize(true);
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $col++;
            }
            $row++;
        }

        // set the content type header for Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.str_replace(' ', '_', $title).'.xlsx"');
        header('Cache-Control: max-age=0');
        // create a writer for Excel and send the output to the browser
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } catch (Exception $e) {
        // saves error log
        log_error($e->getMessage());
        echo "<p>Si è verificato un errore durante l'esportazione del file.</p>";
        exit;
    }
}


// composes and sends an email using received arguments 
function send_email($subject, $message, $sender_mail, $sender_name){

    require 'vendor\phpmailer\phpmailer\src\Exception.php';
    require 'vendor\phpmailer\phpmailer\src\PHPMailer.php';
    require 'vendor\phpmailer\phpmailer\src\SMTP.php';

    // creates an instance of PHPMailer
    $mail = new PHPMailer();

    $send_to = ""; //admin mail
    $pw = ""; // password admin mail 
    $host = ""; //host admin mail
    
    //$mail->SMTPDebug = 2;
    try {
        // SMTP server configuration
        $mail->isSMTP();
        $mail->Host       = $host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $send_to;
        $mail->Password   = $pw;
        $mail->SMTPSecure = '';  //tls, ssl o STARTTLS 
        $mail->Port       = 587;   //465 o 587
        $mail->SMTPAutoTLS = false;

        // email configuration
        $mail->setFrom($sender_mail, $sender_name);  // sender address
        $mail->addReplyTo($sender_mail, $sender_name); 
        $mail->addAddress($send_to, 'Assistenza');  // recipient address
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        // sends email
        if ($mail->send()){
            return true;
        } else {
            $error_message = "Errore durante l'invio dell'email";
            log_error($mail->ErrorInfo);
            return $error_message;
        }

    } catch (Exception $e) {
        $error_message = "Errore durante l'invio dell'email";
        log_error($mail->ErrorInfo);
        return $error_message;
    }
}
?>
