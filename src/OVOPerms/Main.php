<?php

namespace OVOPerms;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class Main extends PluginBase {

    private Config $groups;

    public function onEnable(): void {
        @mkdir($this->getDataFolder());
        $this->groups = new Config($this->getDataFolder() . "groups.yml", Config::YAML);
        $this->getLogger()->info("PurePerms enabled with commands: setgroup, addgroup, ppinfo");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        switch(strtolower($command->getName())) {
            case "setgroup":
                if(count($args) < 2) {
                    $sender->sendMessage("Usage: /setgroup <player> <group>");
                    return true;
                }

                $playerName = $args[0];
                $groupName = $args[1];

                $this->groups->set($playerName, $groupName);
                $this->groups->save();

                $sender->sendMessage("Set $playerName to group $groupName");
                return true;

            case "addgroup":
                if(count($args) < 1) {
                    $sender->sendMessage("Usage: /addgroup <group>");
                    return true;
                }

                $groupName = $args[0];
                $groups = $this->groups->getAll();

                if(isset($groups[$groupName])) {
                    $sender->sendMessage("Group '$groupName' already exists.");
                } else {
                    $this->groups->set($groupName, []);
                    $this->groups->save();
                    $sender->sendMessage("Group '$groupName' added.");
                }
                return true;

            case "ppinfo":
                if(count($args) < 1) {
                    $sender->sendMessage("Usage: /ppinfo <player>");
                    return true;
                }

                $playerName = $args[0];
                $group = $this->groups->get($playerName, "No group set");
                $sender->sendMessage("$playerName is in group: $group");
                return true;

            default:
                return false;
        }
    }
}
