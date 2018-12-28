/**
 * Created by me on 06.06.17.
 */
function subscribe($dummy) {
    $dummy.own().level.subscribeChanged(function(newValue, oldValue) {
        if(newValue == oldValue) {
            return;
        }
        if(_default['level'][newValue] === undefined) {
            $dummy.own().level(parseInt(oldValue));
            return;
        }

        $dummy.own().up(0);
        if($dummy.own().endurance() < _default['level'][newValue]['min']['endurance']) {
            $dummy.own().endurance(_default['level'][newValue]['min']['endurance']);
        }

        $dummy.own().level(parseInt(newValue));
    });

    $dummy.own().up.subscribeChanged(function(newValue, oldValue) {
        if(newValue == oldValue) {
            return;
        }
        if(_default['level'][$dummy.own().level()]['up'][newValue] === undefined) {
            $dummy.own().up(parseInt(oldValue));
            return;
        }

        $dummy.own().up(parseInt(newValue));
    });
    $.each(['strange','agility','intuition','endurance'], function(i, param) {
        $dummy.own()[param].subscribeChanged(function(newValue, oldValue) {
            if(newValue == oldValue) {
                return;
            }
            newValue = parseInt(newValue);
            if(_default['level'][$dummy.own().level()]['min'][param] > newValue) {
                $dummy.own()[param](parseInt(_default['level'][$dummy.own().level()]['min'][param]));
                return;
            }

            $dummy.own()[param](newValue);
        });
    });
    $.each(_default['name']['possession'], function(i, param) {
        $dummy.own()[param].subscribeChanged(function(newValue, oldValue) {
            if(newValue == oldValue) {
                return;
            }
            newValue = parseInt(newValue);
            if(newValue > 5) {
                $dummy.own()[param](5);
                return;
            }

            $dummy.own()[param](newValue);
        });
    });
    $.each(_default['name']['m_possession'], function(i, param) {
        $dummy.own()[param].subscribeChanged(function(newValue, oldValue) {
            if(newValue == oldValue) {
                return;
            }
            newValue = parseInt(newValue);

            $dummy.own()[param](newValue);
        });
    });
}