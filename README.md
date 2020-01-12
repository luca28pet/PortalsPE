# PortalsPE
A PocketMine-MP portal plugin.

Latest release: https://poggit.pmmp.io/p/PortalsPE/

Latest development phars: https://poggit.pmmp.io/ci/luca28pet/PortalsPE/PortalsPE

**How to create a portal:**
- _/portal pos1_, then break the first position
- _/portal pos2_, then break the second position
- Go to where you want the destination of the portal to be, and do _/portal create portalName_

**Flags:**
Every portal has a set of flag which can modify its behaviour.
Current list of implemented flags:
- _teleport_: true/false. If false, the portal will not teleport the player to the destination, but will still execute commands (useful e.g. when you want to transfer player to another server)
- _permissionMode:_ true/false. If true, the players must have the permission _portalspe.portal.portalname_ to use the portal
- _autoload_: true/false. If true, the plugin will try to automatically load the destination world
- _commands_: the list of commands that will be executed when the player enters the portal. You can use {portal} and {player} variables which will be the portal name and the player name

See Commands section belows to see how to edit flags.

**Commands:**
- _/portal pos1_
- _/portal pos2_
- _/portal create <portal name>_
- _/portal list_: see the name of the created portals
- _/portal delete <portal name>_: deletes a portal
- _/portal flag teleport <true|false>_: enables/disables flag teleport
- _/portal flag permissionMode <true|false>_: enables/disables permission mode
- _/portal flag autoload <true|false>_: enables/disables world auto loading
- _/portal flag addcommand <a command>_: adds a command to the portal
- _/portal flag rmcommand <a command>_: removes a command from the portal
- _/portal flag listcommands_: list the commands

**Permissions:**
- portalspe.command.portal: access the main command
- portalspe.portal.portalName: access to portalName

**Config:**

```
---
#Do not edit this
version: 1

#Choose how the plugin detects players movement: use "event" to use PlayerMoveEvent or "task" to use a repeating task
#Best choice may be "task"
movement-detection: "task"

#Task time in seconds (only matters when "task" is selected in the previous field)
task-time: 3

#Language for players messages. You can use {player} and {portal} variables
lang:
  error: "The destination of this portal is in a world that does not exist or is not loaded"
  no-perm: "You do not have permission to use this portal"
  success: "You have entered the portal"
...
```
