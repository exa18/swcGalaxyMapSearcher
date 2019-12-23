# swcGalaxyMapSearcher
swCombine GalaxyMap convert to DB for easeir access to searched data, https://www.swcombine.com/

## MAP Level
https://www.swcombine.com/rules/?Galaxy_Map

#### search links in GalaxyMap
```
preg_match('/href=\"([\/\?\&;=\d\w\s]*)\".*alt=\"([\w]*)\"/', $input_line, $output_array);
href=\"([\/\?\&;=\d\w]*)\".*alt=\"([\w]*)\"
```
this is OK:
```
preg_match_all('/href=\"([\/\?\&;=\d\w]*)\".*alt=\"(.*)\"\s/', $input_lines, $output_array);
```
but this too and simpler:
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

## SYSTEM Level
#### Planets list:
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
preg_match_all('/reg_pointCaption\((.*\d)\)/', $input_lines, $output_array);
```

```
bjMap.reg_areaCaption("Forest",6,19,12,19);
objMap.reg_areaCaption("Ocean",13,19,19,19);
objMap.reg_pointCaption("Thyferra 09-00",9,0);
objMap.reg_pointCaption("Sheridan",10,0);
```

## Instalation

#### Database
###### Level 0: Sectors
```
CREATE TABLE `sector` ( `sectorID` INT NOT NULL , `sectorName` TEXT NOT NULL ) ENGINE = InnoDB; 
```
###### Level 1: Systems
```
CREATE TABLE `system` ( `sectorID` INT NOT NULL , `systemID` INT NOT NULL , `systemName` TEXT NOT NULL , `systemPosition` TEXT NOT NULL , `systemSuns` TEXT NOT NULL , `systemPlanets` TEXT NOT NULL , `systemMoons` TEXT NOT NULL , `systemAsteroidFields` TEXT NOT NULL , `systemStations` TEXT NOT NULL , `systemPopulation` TEXT NOT NULL , `systemControlledBy` TEXT NOT NULL ) ENGINE = InnoDB; 
```
###### Level 2: Planets list
```
CREATE TABLE `planet` ( `systemID` INT NOT NULL , `planetID` INT NOT NULL , `planetName` TEXT NOT NULL , `planetPosition` TEXT NOT NULL , `planetType` TEXT NOT NULL , `planetSize` TEXT NOT NULL , `planetPopulation` TEXT NOT NULL , `planetControlledBy` TEXT NOT NULL , `planetHomeworld` TEXT NOT NULL ) ENGINE = InnoDB; 
```
###### Level 2: Stations list
```
CREATE TABLE `station` ( `systemID` INT NOT NULL , `stationName` TEXT NOT NULL , `stationPosition` TEXT NOT NULL , `stationType` TEXT NOT NULL , `stationOwner` TEXT NOT NULL ) ENGINE = InnoDB; 
```
###### Level 3: Surface
```
CREATE TABLE `surface` ( `planetID` INT NOT NULL , `planetCaption` TEXT NOT NULL , `planetPosition` TEXT NOT NULL ) ENGINE = InnoDB; 
```
## Performance
1. Read systems and its inside takes: ~4,5 minutes