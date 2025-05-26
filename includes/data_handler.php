<?php
class DataHandler {
    private $dataFile;

    public function __construct() {
        $this->dataFile = __DIR__ . '/../data/events.json';
        $this->initializeDataFile();
    }

    private function initializeDataFile() {
        if (!file_exists(__DIR__ . '/../data')) {
            mkdir(__DIR__ . '/../data', 0777, true);
        }
        if (!file_exists($this->dataFile)) {
            $this->saveData([]);
        }
    }

    private function loadData() {
        $jsonData = file_get_contents($this->dataFile);
        return json_decode($jsonData, true) ?? [];
    }

    private function saveData($data) {
        file_put_contents($this->dataFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function getAllEvents() {
        return $this->loadData();
    }

    public function getEvent($id) {
        $events = $this->loadData();
        foreach ($events as $event) {
            if ($event['id'] == $id) {
                return $event;
            }
        }
        return null;
    }

    public function createEvent($eventData) {
        $events = $this->loadData();
        $newId = count($events) > 0 ? max(array_column($events, 'id')) + 1 : 1;
        
        $newEvent = [
            'id' => $newId,
            'title' => $eventData['eventTitle'],
            'description' => $eventData['description'] ?? '',
            'welcome_message' => $eventData['welcomeMessage'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $events[] = $newEvent;
        $this->saveData($events);
        return $newEvent;
    }

    public function updateEvent($id, $eventData) {
        $events = $this->loadData();
        foreach ($events as $key => $event) {
            if ($event['id'] == $id) {
                $events[$key] = array_merge($event, [
                    'title' => $eventData['eventTitle'],
                    'description' => $eventData['description'] ?? $event['description'],
                    'welcome_message' => $eventData['welcomeMessage'] ?? $event['welcome_message'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $this->saveData($events);
                return $events[$key];
            }
        }
        return null;
    }

    public function deleteEvent($id) {
        $events = $this->loadData();
        foreach ($events as $key => $event) {
            if ($event['id'] == $id) {
                array_splice($events, $key, 1);
                $this->saveData($events);
                return true;
            }
        }
        return false;
    }
}
?>
