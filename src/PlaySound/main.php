<?PHP
namespace PlaySound;
//必須
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
//Command
use pocketmine\command\{
	Command, CommandExecutor, CommandSender
};

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\StopSoundPacket;

class Main extends PluginBase implements Listener{

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getLogger()->info("PlaySound was enabled.");  
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args):bool{
        switch (strtolower($command->getName())) {
            case "playsound":
                if($sender instanceof Player){
                    if(!isset($args[0])){
                        $sender->sendMessage("コマンドの引数が不足しています。");
                        return false;
                    }
                    $command=new PlaySound($sender,$args[0],(isset($args[1]))?$args[1]:"",(isset($args[2]))?$args[2]:0,(isset($args[3]))?$args[3]:0,(isset($args[4]))?$args[4]:0,(isset($args[5]))?$args[5]:0);
                    $command->excute(); 
                    return true;
                }else{
                    $sender->sendMessage("コンソールからは実行できません。");
                    return false;
                }
            break;
            case "stopsound":
                if($sender instanceof Player){
                    if(!isset($args[0])){
                        $sender->sendMessage("コマンドの引数が不足しています。");
                        return false;
                    } 
                    $command=new StopSound($sender,$args[0],(isset($args[1]))?$args[1]:"");
                    $command->excute();
                    return true;
                }else{
                    $sender->sendMessage("コンソールからは実行できません。");
                    return false;
                }
            break;
        }   
        return false;
    }
}

class PlaySound{

    private $sender;
    private $soundName;
    private $player;
    private $x,$y,$z;
    private $volume;
    private $pitch;

    public function __construct($sender, $soundName = "", $player = "", $x = 0, $y = 0, $z = 0, $volume = 1.0, $pitch = 1.0){
        $this->sender = $sender;
        $this->soundName = $soundName;
        $this->player = $player;
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->volume = $volume;
        $this->pitch = $pitch;
    }

    public function excute(){
        $pk=new PlaySoundPacket;
        $pk->soundName = $this->soundName;
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->volume = ((float)$this->volume)*100;
        $pk->pitch = (float)$this->pitch;
        if($this->player==="@a"){
            $this->sender->getServer()->broadcastPacket($this->sender->getServer()->getOnlinePlayers(),$pk);           
        }else if($this->player===""){
            $this->sender->dataPacket($pk);
        }else{
            $plist=$this->sender->getServer()->matchPlayer($this->player);
            if($plist!=[]){
                $this->sender->getServer()->broadcastPacket($plist,$pk);
            }else{
                $this->sender->sendMessage("対象がいません。");
            }
        }
        $this->sender->sendMessage("コマンドを実行しました。");
        return true;
    }
}

class StopSound{

    private $sender;
    private $player;
    private $soundName;

    public function __construct($sender, $player = "", $soundName = ""){
        $this->sender = $sender;
        $this->player = $player;
        $this->soundName = $soundName;
    }

    public function excute(){
        $pk=new StopSoundPacket;
        $pk->soundName = $this->soundName;
        $pk->stopAll = ($this->soundName==="")?true:false;
        if($this->player==="@a"){
            $this->sender->getServer()->broadcastPacket($this->sender->getServer()->getOnlinePlayers(),$pk);           
        }else if($this->player===""){
            $this->sender->dataPacket($pk);
        }else{
            $plist=$this->sender->getServer()->matchPlayer($this->player);
            if($plist!=[]){
                $this->sender->getServer()->broadcastPacket($plist,$pk);
            }else{
                $this->sender->sendMessage("対象がいません。");
            }
        }
        $this->sender->sendMessage("コマンドを実行しました。");
        return true;
    }
}
?>