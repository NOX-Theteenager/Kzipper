package com.sportstracker.sport.Config;

import org.springframework.stereotype.Component;

import java.security.SecureRandom;
import java.util.Base64;
import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

@Component
public class PasswordUtil {

    public static String generateSalt() {
        SecureRandom random = new SecureRandom();
        byte[] salt = new byte[16];
        random.nextBytes(salt);
        return Base64.getEncoder().encodeToString(salt);
    }

    public static String hashPassword(String plainPassword, String salt) throws NoSuchAlgorithmException {
        MessageDigest digest = MessageDigest.getInstance("SHA-256");
        String saltedPassword = plainPassword + salt;
        byte[] hash = digest.digest(saltedPassword.getBytes(StandardCharsets.UTF_8));
        StringBuilder hexString = new StringBuilder();
        for (byte b : hash) {
            String hex = Integer.toHexString(0xff & b);
            if (hex.length() == 1) hexString.append('0');
            hexString.append(hex);
        }
        return hexString.toString();
    }

    public static boolean verifyPassword(String plainPassword, String salt, String hashedPassword) throws NoSuchAlgorithmException {
        // Hachez le mot de passe saisi avec le même sel
        String computedHash = hashPassword(plainPassword, salt);
        // Comparez le hash calculé avec le hash stocké
        return computedHash.equals(hashedPassword);
    }

}
