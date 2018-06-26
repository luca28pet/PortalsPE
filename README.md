# PortalsPE
A PocketMine-MP portal plugin.

Latest release: https://poggit.pmmp.io/p/PortalsPE/

Latest development phars: https://poggit.pmmp.io/ci/luca28pet/PortalsPE/PortalsPE

**How to create a portal:**
- _/portal pos1_, then break the first position
- _/portal pos2_, then break the second position
- Go to where you want the destination of the portal to be, and do _/portal create portalName_
- If you want to fill the portal with a block (for example water), do _/portal fill portalName blockId_ (the id must be numeric)

**Commands:**
- _/portal pos1_
- _/portal pos2_
- _/portal create portalName_
- _/portal list_: see the name of the created portals
- _/portal delete portalName_: deletes a portal
- _/portal fill portalName blockId_: fill a portal with a block

**Permissions:**
- portalspe.admin: access the main command

**Config:**

```---
#Choose how the plugin detects players movement: use "event" to use PlayerMoveEvent or "task" to use a repeating task
#Best choice may be "event"
movement-detection: "event"

#Task time in seconds (only matters when "task" is selected in the previous field)
task-time: 3

#Automatically loads worlds if they are not loaded when a player enters a portal (true or false)
auto-load: true

#If you set this to true, the plugin will check if the player has the right permission to use the portal
#For example, if a player enters the portal "survival", he will be teleported only if he has the permission "portal.survival" (you can use a permission manager like PurePerms)
#Otherwise, if this is false, every player can use every portal
permission-mode: false

#Language for players messages
message-error: "The destination of this portal is in a world that does not exixts or is not loaded"
message-tp: "You have entered the portal"
message-no-perm: "You do not have permission to use this portal"
...
