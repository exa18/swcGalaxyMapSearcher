# swcGalaxyMapSearcher
[swCombine GalaxyMap](https://www.swcombine.com/) download localy for easeir/faster access to searched data
Using [API 2.0](https://www.swcombine.com/ws/v2.0/)


## MAP
What data are downloaded?

### SECTOR
```
    uid
    name
    href
    controlledby
    knownsystems
    population
```
### SYSTEM
```
    uid
    name
    href
    population
    controlledby
    location->sector
        uid
    location->coordinates->galaxy
        x
        y
```
### FACTION
```
    name
    href
```
## Refresh performance

SECTORs : ~30s\
SYSTEMs : ~60s


## API v2.0 guides

#### TYPES

```
20 : faction
8 : planet
7 : city
9 : system
25 : sector
24 : terrain
1 : user
22 : race
5 : station
18 : weapon
16 : material
3 : vehicles
13 : droids
4 : facility
12 : items
10 : npc
11 : creatures
```

#### LINKS

##### GALAXY
[cities](https://www.swcombine.com/ws/v2.0/galaxy/cities/)
[systems](https://www.swcombine.com/ws/v2.0/galaxy/systems/)
[sectors](https://www.swcombine.com/ws/v2.0/galaxy/sectors/)
[factions](https://www.swcombine.com/ws/v2.0/factions/)
##### TYPES
[planets](https://www.swcombine.com/ws/v2.0/types/planets)
[terrain](https://www.swcombine.com/ws/v2.0/types/terrain)
[races](https://www.swcombine.com/ws/v2.0/types/races)
[stations](https://www.swcombine.com/ws/v2.0/types/stations)
[weapons](https://www.swcombine.com/ws/v2.0/types/weapons)
[materials](https://www.swcombine.com/ws/v2.0/types/materials)
[vehicles](https://www.swcombine.com/ws/v2.0/types/vehicles)
[droids](https://www.swcombine.com/ws/v2.0/types/droids)
[facilities](https://www.swcombine.com/ws/v2.0/types/facilities)
[items](https://www.swcombine.com/ws/v2.0/types/items)
[npcs](https://www.swcombine.com/ws/v2.0/types/npcs)
[creatures](https://www.swcombine.com/ws/v2.0/types/creatures)

