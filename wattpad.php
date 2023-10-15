<?php
// Function to fetch and extract data
function fetchData($url) {
    try {
        // Fetch the web page's content
        $html = file_get_contents($url);
        
        // Create a DOMDocument to parse the HTML
        $doc = new DOMDocument();
        @$doc->loadHTML($html); // Use @ to suppress warnings (if any)

        // Create a DOMXPath instance
        $xpath = new DOMXPath($doc);

        // Find all the list items with class "list-group-item"
        $listItems = $xpath->query('//div[contains(@class, "list-group-item")]');

        // Create an empty array to store the list items
        $itemList = [];

        // Iterate over the found list items and extract data
        foreach ($listItems as $item) {
            $dataId = $item->getAttribute('data-id');
            $link = 'https://www.wattpad.com' . $item->getElementsByTagName('a')->item(0)->getAttribute('href');
            $imageSrc = $item->getElementsByTagName('img')->item(0)->getAttribute('src');
            $name = $item->getElementsByTagName('a')->item(1)->textContent;
            $views = trim($item->getElementsByTagName('span')->item(0)->textContent);
            $votes = trim($item->getElementsByTagName('span')->item(1)->textContent);
            $chapters = trim($item->getElementsByTagName('span')->item(2)->textContent);
            $description = trim($item->getElementsByTagName('div')->item(1)->textContent);
            
            // Check if there are label elements in story-status
            $storyStatus = $item->getElementsByTagName('div')->item(2);
            $labels = $storyStatus->getElementsByTagName('span');
            $statusLabels = [];
            foreach ($labels as $label) {
                if ($label->getAttribute('class') == 'label label-danger') {
                    $statusLabels[] = 'Mature';
                } elseif ($label->getAttribute('class') == 'label label-info') {
                    $statusLabels[] = 'Complete';
                }
            }

            // Create an object with extracted data
            $itemData = [
                'dataId' => $dataId,
                'link' => $link,
                'imageSrc' => $imageSrc,
                'name' => $name,
                'views' => $views,
                'votes' => $votes,
                'chapters' => $chapters,
                'description' => $description,
                'statusLabels' => $statusLabels,
            ];

            // Add the object to the array
            $itemList[] = $itemData;
        }

        // Output the result as JSON
        echo json_encode($itemList, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        echo "Error fetching data: " . $e->getMessage();
    }
}

// Call the fetchData function with the URL parameter
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    fetchData($url);
} else {
    echo "Please provide a URL parameter.";
}
?>
