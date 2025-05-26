<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'data_handler.php';

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');

// Initialize data handler
$dataHandler = new DataHandler();

// Get the raw input data
$rawInput = file_get_contents('php://input');
error_log("Raw input: " . $rawInput);

// Parse JSON input
$jsonData = json_decode($rawInput, true);
error_log("Decoded JSON: " . print_r($jsonData, true));

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

error_log("Method: " . $method);
error_log("Action: " . $action);

switch ($method) {
    case 'GET':
        if ($action == 'get_event') {
            get_event($dataHandler);
        } else {
            get_all_events($dataHandler);
        }
        break;
    case 'POST':
        create_event($dataHandler, $jsonData);
        break;
    case 'PUT':
        update_event($dataHandler, $jsonData);
        break;
    case 'DELETE':
        delete_event($dataHandler, $jsonData);
        break;
    default:
        send_response(false, null, 'Invalid request method');
}

function send_response($success, $data = null, $message = '') {
    $response = [
        'success' => $success,
        'data' => $data,
        'message' => $message
    ];
    error_log("Sending response: " . print_r($response, true));
    echo json_encode($response);
    exit;
}

function get_all_events($dataHandler) {
    try {
        $events = $dataHandler->getAllEvents();
        send_response(true, $events);
    } catch(Exception $e) {
        error_log("Error in get_all_events: " . $e->getMessage());
        send_response(false, null, "Error fetching events: " . $e->getMessage());
    }
}

function get_event($dataHandler) {
    if (!isset($_GET['id'])) {
        send_response(false, null, 'Event ID is required');
    }
    
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    
    try {
        $event = $dataHandler->getEvent($id);
        if ($event) {
            send_response(true, $event);
        } else {
            send_response(false, null, 'Event not found');
        }
    } catch(Exception $e) {
        error_log("Error in get_event: " . $e->getMessage());
        send_response(false, null, "Error fetching event: " . $e->getMessage());
    }
}

function create_event($dataHandler, $data) {
    if (!$data) {
        $data = $_POST;
    }

    $eventTitle = $data['eventTitle'] ?? '';
    $description = $data['description'] ?? '';
    $welcomeMessage = $data['welcomeMessage'] ?? '';
    
    if (empty($eventTitle)) {
        send_response(false, null, 'Event title is required');
    }
    
    try {
        $eventData = [
            'eventTitle' => $eventTitle,
            'description' => $description,
            'welcomeMessage' => $welcomeMessage
        ];
        
        error_log("Creating event with data: " . print_r($eventData, true));
        $newEvent = $dataHandler->createEvent($eventData);
        send_response(true, $newEvent, 'Event created successfully');
    } catch(Exception $e) {
        error_log("Error in create_event: " . $e->getMessage());
        send_response(false, null, "Error creating event: " . $e->getMessage());
    }
}

function update_event($dataHandler, $data) {
    error_log("Update event data received: " . print_r($data, true));
    
    if (!$data) {
        error_log("No data received for update");
        send_response(false, null, 'Invalid JSON data');
        return;
    }
    
    $id = $data['id'] ?? '';
    $eventTitle = $data['eventTitle'] ?? '';
    
    if (empty($id) || empty($eventTitle)) {
        error_log("Missing required fields - ID: $id, Title: $eventTitle");
        send_response(false, null, 'Event ID and title are required');
    }
    
    try {
        $eventData = [
            'eventTitle' => $eventTitle,
            'description' => $data['description'] ?? '',
            'welcomeMessage' => $data['welcomeMessage'] ?? ''
        ];
        
        error_log("Updating event $id with data: " . print_r($eventData, true));
        $updatedEvent = $dataHandler->updateEvent($id, $eventData);
        if ($updatedEvent) {
            send_response(true, $updatedEvent, 'Event updated successfully');
        } else {
            send_response(false, null, 'Event not found or no changes made');
        }
    } catch(Exception $e) {
        error_log("Error in update_event: " . $e->getMessage());
        send_response(false, null, "Error updating event: " . $e->getMessage());
    }
}

function delete_event($dataHandler, $data) {
    if (!$data) {
        error_log("No data received for delete");
        send_response(false, null, 'Invalid JSON data');
        return;
    }
    
    $id = $data['id'] ?? '';
    
    if (empty($id)) {
        send_response(false, null, 'Event ID is required');
    }
    
    try {
        error_log("Attempting to delete event $id");
        if ($dataHandler->deleteEvent($id)) {
            send_response(true, null, 'Event deleted successfully');
        } else {
            send_response(false, null, 'Event not found');
        }
    } catch(Exception $e) {
        error_log("Error in delete_event: " . $e->getMessage());
        send_response(false, null, "Error deleting event: " . $e->getMessage());
    }
}
?>
