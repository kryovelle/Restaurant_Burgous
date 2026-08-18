<?php
require_once __DIR__ ."/../View/HomePageView.php";
require_once __DIR__. "/../Model/HomePageModel.php";


Class HomePageController{


 function organizeDaysByTime($data) {
    $grouped = [];
    
    // Map full day names to short names
    $dayMap = [
        'Monday' => 'Mon',
        'Tuesday' => 'Tue',
        'Wednesday' => 'Wed',
        'Thursday' => 'Thu',
        'Friday' => 'Fri',
        'Saturday' => 'Sat',
        'Sunday' => 'Sun'
    ];
    
    // Define the correct order for sorting
    $dayOrder = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    
    foreach ($data as $row) {
        $day = trim($row['day_of_week']);
        
        // Skip if empty
        if (empty($day)) {
            continue;
        }
        
        // Convert full day name to short name if needed
        if (isset($dayMap[$day])) {
            $day = $dayMap[$day];
        }
        
        // Format time from '15:00:00' to '15.00'
        $open_time = $this->formatTime($row['open_time']);
        $close_time = $this->formatTime($row['close_time']);
        
        $key = $open_time . '|' . $close_time;
        
        if (!isset($grouped[$key])) {
            $grouped[$key] = [
                'days' => [],
                'open_time' => $open_time,
                'close_time' => $close_time
            ];
        }
        
        $grouped[$key]['days'][] = $day;
    }
    
    $result = [];
    foreach ($grouped as $group) {
        // Sort days in chronological order
        usort($group['days'], function($a, $b) use ($dayOrder) {
            return array_search($a, $dayOrder) - array_search($b, $dayOrder);
        });
        
        $result[] = [
            'days' => implode(',', $group['days']),
            'open_time' => $group['open_time'],
            'close_time' => $group['close_time']
        ];
    }
    
    return $result;
}

// Helper function to format time
function formatTime($time) {
    // If it's already in 'HH:MM:SS' format, convert to 'HH.MM'
    if (strpos($time, ':') !== false) {
        $parts = explode(':', $time);
        return $parts[0] . '.' . $parts[1]; // Returns '15.00' from '15:00:00'
    }
    return $time; // Keep as is if already formatted
}

  function displayHomePage(){
    $model= new HomePageModel();
    $contact_details=$model->getContactDetails();
    $menu_categories=$model->getMenuCategories();
    $opening_hours=$this->organizeDaysByTime($model->getOpeningHours());
    $items=$model->getRecommendedMenuItems();
   

    $view = new HomePageView();
    $view->displayHomePageView($contact_details,$opening_hours,$menu_categories,$items);
  }
}

?>