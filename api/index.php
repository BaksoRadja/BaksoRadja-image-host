<?php

// Function to display images using readfile
function displayImage($imgPath)
{
    if (file_exists($imgPath)) {
        header('Content-Type: ' . mime_content_type($imgPath));
        readfile($imgPath);
        exit;
    }
}

// Get image names from the 'img' directory
$imgDirectory = __DIR__ . '/../img';
$subFolders = scandir($imgDirectory, SCANDIR_SORT_ASCENDING);

// Remove "." and ".." from the list
$subFolders = array_diff($subFolders, array('.', '..'));

// Search query
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Check if a subfolder is selected
$selectedSubfolder = isset($_GET['subfolder']) ? $_GET['subfolder'] : null;

// Get the list of images based on the selected subfolder
$imgFiles = [];
if ($selectedSubfolder !== null && in_array($selectedSubfolder, $subFolders)) {
    $subfolderPath = $imgDirectory . '/' . $selectedSubfolder;
    $imgFiles = scandir($subfolderPath, SCANDIR_SORT_ASCENDING);
    $imgFiles = array_diff($imgFiles, array('.', '..'));

    // Filter files based on search query
    if ($searchQuery !== '') {
        $imgFiles = array_filter($imgFiles, function ($imgFile) use ($searchQuery) {
            return strpos($imgFile, $searchQuery) !== false;
        });
    }
}
// Calculate total size for all folders
$totalAll = 0;
foreach ($subFolders as $folder) {
    $folderPath = $imgDirectory . '/' . $folder;
    $folderSize = 0;

    if (is_dir($folderPath)) {
        $folderFiles = scandir($folderPath, SCANDIR_SORT_ASCENDING);
        $folderFiles = array_diff($folderFiles, array('.', '..'));

        foreach ($folderFiles as $file) {
            $filePath = $folderPath . '/' . $file;
            if (is_file($filePath)) {
                $folderSize += filesize($filePath);
            }
        }

        $totalAll += $folderSize;
    }
}

$totalAll = round($totalAll / 1024 / 1024, 2);

// Total Img Size In Sub Folder in MB
$totalSize = 0;
foreach ($imgFiles as $imgFile) {
    $totalSize += filesize($imgDirectory . '/' . $selectedSubfolder . '/' . $imgFile);
}
$totalSize = round($totalSize / 1024 / 1024, 2);

// Get base URL
$baseUrl  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
$baseUrl .= $_SERVER['HTTP_HOST'];

// Check if image parameter is provided
if (isset($_GET['img'])) {
    $imgPath = urldecode($_GET['img']);
    displayImage($imgPath);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery</title>
    <link rel="stylesheet" href="/css/index.css">

</head>

<body>
    <div class="main">
        <!-- img hosting -->
        <h1>Image List Host Github Create By Mpiie :]</h1>
        <div class="row">
            <h3>Total Size: <div class="blue"> <?php echo $totalAll; ?> </div> MB
            </h3>
            <h3>Size Sub Folder: <div class="blue"><?php echo $totalSize; ?></div> MB
            </h3>
            <h3>Total Images: <div class="blue"><?php echo count($imgFiles); ?></div>
            </h3>
            <h3>Subfolder: <div class="blue"><?php echo $selectedSubfolder; ?></div>
            </h3>
        </div>
        <!-- Search form -->
        <form action="" method="get">
            <label for="search">Search File Name:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <input type="hidden" id="selectedSubfolder" name="subfolder" value="<?php echo htmlspecialchars($selectedSubfolder); ?>">
            <button type="button" onclick="searchImages()">Search</button>
        </form>

        <!-- List of subfolders -->
        <div class="folder-list">
            <h2>List of Subfolders:</h2>
            <ul>
                <?php foreach ($subFolders as $folder) :
                    $active = $folder === $selectedSubfolder ? 'active' : 'default';
                ?>
                    <li class="<?php echo $active; ?>">
                        <a href="?subfolder=<?php echo urlencode($folder); ?>" onclick="document.getElementById('selectedSubfolder').value='<?php echo urlencode($folder); ?>';"><?php echo $folder; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($selectedSubfolder !== null) : ?>
            <!-- Form Upload File -->
            <!-- <form action="upload.php" method="post" enctype="multipart/form-data">
                <label for="file">Upload File:</label>
                <input type="file" id="file" name="file">
                <button type="submit" name="submit">Upload</button>
            </form> -->

            <!-- <?php if (isset($_GET['status']) && $_GET['status'] === 'success_upload') : ?>
                <p class="upload-status">File uploaded successfully.</p>
            <?php endif; ?>
 -->

            <!-- Display the list of images -->
            <div class="container-img">
                <?php foreach ($imgFiles as $imgFile) : ?>
                    <?php
                    // Get img size for each image
                    $imgSize = getimagesize($imgDirectory . '/' . $selectedSubfolder . '/' . $imgFile);
                    // Get img width for each image
                    $imgWidth = $imgSize[0];
                    // Get img height for each image
                    $imgHeight = $imgSize[1];
                    ?>
                    <div class="list-img">
                        <img src="?img=<?php echo urlencode($imgDirectory . '/' . $selectedSubfolder . '/' . $imgFile); ?>" alt="<?php echo $imgFile; ?>" style="max-width: 300px; max-height: 300px;">
                        <!-- Display img info -->
                        <p>File name: <?php echo $imgFile; ?></p>
                        <p>File size: <?php echo filesize($imgDirectory . '/' . $selectedSubfolder . '/' . $imgFile); ?> bytes or <?php echo round(filesize($imgDirectory . '/' . $selectedSubfolder . '/' . $imgFile) / 1024, 2); ?> KB or <?php echo round(filesize($imgDirectory . '/' . $selectedSubfolder . '/' . $imgFile) / 1024 / 1024, 2); ?> MB</p>
                        <p>File width: <?php echo $imgWidth; ?> px</p>
                        <p>File height: <?php echo $imgHeight; ?> px</p>
                        <!-- img link -->
                        <p>Link: <a href="<?php echo $baseUrl . '/img/' . $selectedSubfolder . '/' . $imgFile; ?>" target="_blank"><?php echo $baseUrl . '/img/' . $selectedSubfolder . '/' . $imgFile; ?></a></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <script>
        function searchImages() {
            var searchQuery = document.getElementById('search').value;
            var subfolder = document.getElementById('selectedSubfolder').value;
            window.location.href = '?search=' + encodeURIComponent(searchQuery) + '&subfolder=' + encodeURIComponent(subfolder);
        }
    </script>
</body>

</html>