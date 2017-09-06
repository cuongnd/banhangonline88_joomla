package vantinviet.core.libraries.joomla.string;

import java.util.Random;

/**
 * Created by cuong on 9/5/2017.
 */

public class JString {
    public static String generateRandomString(int length) {
        char[] chars = "abcdefghijklmnopqrstuvwxyz0123456789".toCharArray();
        StringBuilder sb = new StringBuilder();
        Random random = new Random();
        for (int i = 0; i < 20; i++) {
            char c = chars[random.nextInt(chars.length)];
            sb.append(c);
        }
        return   sb.toString();
    }
}
