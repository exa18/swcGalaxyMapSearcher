# swcGalaxyMapSearcher
swCombine GalaxyMap convert to DB for easeir access to searched data, https://www.swcombine.com/


#### search links in GalaxyMap
```
preg_match('/href=\"([\/\?\&;=\d\w\s]*)\".*alt=\"([\w]*)\"/', $input_line, $output_array);
href=\"([\/\?\&;=\d\w]*)\".*alt=\"([\w]*)\"
```
to jest OK:
```
preg_match_all('/href=\"([\/\?\&;=\d\w]*)\".*alt=\"(.*)\"\s/', $input_lines, $output_array);
```
lub to:
```
preg_match_all('/href=\"(.*)\".*alt=\"(.*)\"\s/', $input_lines, $output_array);
```
getID
```
preg_match('/(\d+)/', $input_line, $output_array);
```

## SECTOR Level:
(list systems)
```
<table class="rulesTable">
<tr>
	<th>Name</th>
	<th>Position</th>
	<th>Suns</th>
	<th>Planets</th>
	<th>Moons</th>
	<th>Asteroid Fields</th>
	<th>Stations</th>
	<th>Population</th>
	<th>Controlled By</th>
</tr>
<tr>
	<td><a href="/rules/?Galaxy_Map&amp;systemID=1125">Polith</a></td>
	<td>15, -136</td>
	<td>1</td>
	<td>6</td>
	<td>1</td>
	<td>4</td>
	<td>5</td>
	<td>6,334,508,417</td>
	<td><a href="/community/factions.php?facName=Galactic+Empire">Galactic Empire</a></td>
</tr>
</table>
```
///LUB:
```
<table class="rulesTable sortable" align="left">
<tr>
    <th>Image</th>
    <th>Name</th>
    <th>Position</th>
    <th>Type</th>
    <th>Size</th>
    <th>Population</th>
    <th>Controlled By</th>
    <th>Homeworld</th>
</tr>
<tr>
    <td><img src="https://img.swcombine.com//galaxy/planets/11/mini.gif" alt="Polith Asteroid Cloud" /></td>
    <td><a href="/rules/?Galaxy_Map&amp;planetID=2864">Polith Asteroid Cloud</a></td>
    <td>1, 11</td>
    <td>Asteroid Field</td>
    <td>1x1</td>
    <td>3,092</td>
    <td><a href="/community/factions.php?facName=Galactic+Empire">Galactic Empire</a></td>
    <td>-</td>
</tr>
</table>
```

## SYSTEM Level
#### Planet list:
```
<table class="rulesTable sortable" align="left">
<tr>
    <th>Image</th>
    <th>Name</th>
    <th>Position</th>
    <th>Type</th>
    <th>Size</th>
    <th>Population</th>
    <th>Controlled By</th>
    <th>Homeworld</th>
</tr>
<tr>
    <td><img src="https://img.swcombine.com//galaxy/planets/11/mini.gif" alt="Polith Asteroid Cloud" /></td>
    <td><a href="/rules/?Galaxy_Map&amp;planetID=2864">Polith Asteroid Cloud</a></td>
    <td>1, 11</td>
    <td>Asteroid Field</td>
    <td>1x1</td>
    <td>3,092</td>
    <td><a href="/community/factions.php?facName=Galactic+Empire">Galactic Empire</a></td>
    <td>-</td>
</tr>
</table>
```
#### Stations list:
```
<table class="rulesTable sortable" align="left">
<tr>
    <th>Image</th>
    <th>Name</th>
    <th>Position</th>
    <th>Type</th>
    <th>Owner</th>
</tr>
<tr>
    <td><img src="https://img.swcombine.com//stations/20/small.gif" alt="Golan II" /></td>
    <td>Polith  Defender Kanakorm</td>
    <td>11, 9</td>
    <td><a href="/rules/?Space_Stations&amp;ID=20">Golan II</a></td>
    <td><a href="/community/factions.php?facName=Galactic+Empire">Galactic Empire</a></td>
</tr>
</table>
```
## PLANET level:
#### City names:
```
preg_match_all('/reg_pointCaption\(\"(.*)\"/', $input_lines, $output_array);
```

```
bjMap.reg_areaCaption("Forest",6,19,12,19);
objMap.reg_areaCaption("Ocean",13,19,19,19);
objMap.reg_pointCaption("Thyferra 09-00",9,0);
objMap.reg_pointCaption("Sheridan",10,0);
```
