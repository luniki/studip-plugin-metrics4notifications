<?php

# Copyright (c)  2015 - <mlunzena@uos.de>
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.

class Metrics4Notifications extends StudipPlugin implements SystemPlugin
{
    static function onEnable($id)
    {
        RolePersistence::assignPluginRoles($id, array(7));
    }

    static function onDisable($id)
    {
        PluginManager::getInstance()->unregisterPlugin($id);
    }


    function __construct()
    {
        parent::__construct();
        NotificationCenter::addObserver($this, 'update', NULL);
    }


    function update($event, $subject) {
        // count every event
        Metrics::increment(
            'plugin.notification.' .
            preg_replace('/[^\w.\/]/', '', strtolower($event))
        );

        // count each activated navigations every 10th time
        if ($event === 'NavigationDidActivateItem') {
            $parts = explode('/', preg_replace('/[^\w.\/]/', '', $subject));
            $stat = 'plugin.visited.' . $parts[1] . ($parts[2] ? ".$parts[2]" : '');
            Metrics::increment($stat, 0.1);
        }
    }
}
