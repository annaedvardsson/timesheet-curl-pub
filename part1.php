<?php

include 'header.html';
include 'nav.html';

include 'credentials.php';
include 'curl.php';

if ($_POST) {
    $_POST = array_filter($_POST, function ($post) {
        return $post !== '';
    });

    $timereport_url .= "?" . http_build_query($_POST);
}

// Fetch info from API
$timereport_results = json_decode(curl($timereport_url, $headers), true);
$workplace_results = json_decode(curl($workplace_url, $headers), true);

// Add workplace name to $timereport_results
for ($i = 0; $i < count($timereport_results); $i++) {
    for ($j = 0; $j < count($workplace_results); $j++) {
        if ($timereport_results[$i]["workplace_id"] == $workplace_results[$j]["id"]) {
            $timereport_results[$i]["workplace"] = $workplace_results[$j]["name"];
            continue;
        }
    }
}

// Sort $timereport_results by "date", "workplace", "id"
usort($timereport_results, function ($a, $b) {
    $dateComparison = strcmp($a['date'], $b['date']);

    if ($dateComparison == 0) {
        $workplaceComparison = strcmp($a['workplace'], $b['workplace']);

        if ($workplaceComparison == 0) {
            return $a['id'] - $b['id'];
        } else {
            return $workplaceComparison;
        }
    } else {
        return $dateComparison;
    }
});

?>

<!--********************************************************************************-->
<h2>Del 1 - Lista tidsrapporter</h2>

<fieldset>
    <legend>Filtrera resultat:</legend>

    <form action="part1.php" method="post">
        <label for="workplace">Arbetsplats:</label>
        <select name="workplace">
            <option value="">Filtrera på arbetsplats...</option>
            <?php foreach ($workplace_results as $workplace) { ?>
                <option value="<?= $workplace['id'] ?>"><?= htmlspecialchars($workplace["name"]) ?></option>
            <?php } ?>
        </select>
        <label for="from_date">Från:</label>
        <input type="date" name="from_date">
        <label for="to_date">Till:</label>
        <input type="date" name="to_date">
        <input type="submit">
    </form>
</fieldset>

<h3>Resultat</h3>
<table>
    <tr>
        <th>Datum</th>
        <th>Arbetsplatsnamn</th>
        <th>Timmar</th>
    </tr>
    <?php foreach ($timereport_results as $timereport) { ?>
        <tr>
            <td><?= $timereport["date"] ?></td>
            <td><?= $timereport["workplace"] ?></td>
            <td><?= $timereport["hours"] ?></td>
        </tr>
    <?php } ?>
</table>

<?php
include 'footer.html';
?>
